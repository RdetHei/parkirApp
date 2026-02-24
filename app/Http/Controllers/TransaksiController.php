<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\User;
use App\Models\AreaParkir;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // == ALUR UTAMA PARKIR ==

    /**
     * Menangani proses kendaraan masuk (check-in).
     * Mendukung kendaraan terdaftar (id_kendaraan) atau kendaraan baru (plat_nomor + jenis_kendaraan).
     * Menggunakan transaction untuk mencegah race condition
     */
    public function checkIn(Request $request)
    {
        // Validasi: either id_kendaraan (terdaftar) atau plat_nomor + jenis_kendaraan (baru)
        $isNewVehicle = $request->filled('vehicle_mode') && $request->vehicle_mode === 'new';

        if ($isNewVehicle) {
            $request->validate([
                'plat_nomor' => 'required|string|max:15',
                'jenis_kendaraan' => 'required|string|max:20',
                'warna' => 'nullable|string|max:20',
                'pemilik' => 'nullable|string|max:100',
                'id_tarif' => 'required|exists:tb_tarif,id_tarif',
                'id_area' => 'required|exists:tb_area_parkir,id_area',
                'parking_map_slot_id' => 'nullable|exists:tb_parking_map_slots,id',
                'catatan' => 'nullable|string|max:255',
            ]);
            $platNormalized = $this->normalizePlatNomor($request->plat_nomor);
            if (Kendaraan::whereRaw('UPPER(REPLACE(plat_nomor, \' \', \'\')) = ?', [$platNormalized])->exists()) {
                return back()->withInput()->with('error', 'Plat nomor sudah terdaftar. Gunakan mode kendaraan terdaftar.');
            }
        } else {
            $request->validate([
                'id_kendaraan' => 'required|exists:tb_kendaraan,id_kendaraan',
                'id_tarif' => 'required|exists:tb_tarif,id_tarif',
                'id_area' => 'required|exists:tb_area_parkir,id_area',
                'parking_map_slot_id' => 'nullable|exists:tb_parking_map_slots,id',
                'catatan' => 'nullable|string|max:255',
            ]);
        }

        if ($request->filled('parking_map_slot_id')) {
            $slot = \App\Models\ParkingMapSlot::find($request->parking_map_slot_id);
            if (!$slot || (int) $slot->area_parkir_id !== (int) $request->id_area) {
                return back()->withInput()->with('error', 'Slot parkir tidak valid untuk area yang dipilih.');
            }
            $occupied = Transaksi::where('parking_map_slot_id', $slot->id)
                ->whereNull('waktu_keluar')
                ->where('status', 'masuk')
                ->exists();
            if ($occupied) {
                return back()->withInput()->with('error', 'Slot ' . $slot->code . ' sudah terisi.');
            }
        }

        try {
            $transaksi = DB::transaction(function () use ($request, $isNewVehicle) {
                $area = AreaParkir::lockForUpdate()->findOrFail($request->id_area);

                if ($area->terisi >= $area->kapasitas) {
                    throw new \Exception('Kapasitas area parkir sudah penuh');
                }

                $id_kendaraan = $request->id_kendaraan;

                if ($isNewVehicle) {
                    $platNormalized = $this->normalizePlatNomor($request->plat_nomor);
                    $kendaraan = Kendaraan::create([
                        'plat_nomor' => $platNormalized,
                        'jenis_kendaraan' => $request->jenis_kendaraan,
                        'warna' => $request->warna,
                        'pemilik' => $request->pemilik,
                        'id_user' => null,
                    ]);
                    $id_kendaraan = $kendaraan->id_kendaraan;
                }

                $transaksi = Transaksi::create([
                    'id_kendaraan' => $id_kendaraan,
                    'id_tarif' => $request->id_tarif,
                    'id_area' => $request->id_area,
                    'parking_map_slot_id' => $request->parking_map_slot_id ?: null,
                    'id_user' => Auth::id(),
                    'waktu_masuk' => Carbon::now(),
                    'status' => 'masuk',
                    'catatan' => $request->catatan,
                ]);

                $area->increment('terisi');

                return $transaksi;
            });

            return redirect()->route('transaksi.parkir.index')
                ->with('success', 'Kendaraan berhasil dicatat masuk parkir. ID Transaksi: ' . $transaksi->id_parkir);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menangani proses kendaraan keluar (check-out).
     * Menggunakan transaction untuk atomic operation
     */
    public function checkOut(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transaksi = Transaksi::lockForUpdate()->findOrFail($id);

                // Validasi status
                if ($transaksi->status === 'keluar') {
                    throw new \Exception('Kendaraan ini sudah tercatat keluar.');
                }

                // Kalkulasi durasi dan biaya
                $waktu_keluar = Carbon::now();
                $durasi_jam = ceil($waktu_keluar->diffInMinutes($transaksi->waktu_masuk) / 60);

                if (!$transaksi->tarif) {
                    return back()->with('error', 'Tarif tidak ditemukan untuk transaksi ini.');
                }
                $biaya_total = $durasi_jam * $transaksi->tarif->tarif_perjam;

                // Update transaksi
                $transaksi->update([
                    'waktu_keluar' => $waktu_keluar,
                    'durasi_jam' => $durasi_jam,
                    'biaya_total' => $biaya_total,
                    'status' => 'keluar',
                    'status_pembayaran' => 'pending', // Status default saat checkout
                ]);

                // Decrement kapasitas area parkir dengan lock
                $area = AreaParkir::lockForUpdate()->findOrFail($transaksi->id_area);
                if ($area->terisi > 0) {
                    $area->decrement('terisi');
                }
            });

            return redirect()->route('payment.select-transaction')
                ->with('success', 'Kendaraan berhasil checkout. Silakan lanjut ke pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal checkout: ' . $e->getMessage());
        }
    }

    // == CRUD UNTUK ADMIN ==

    public function index()
    {
        if (
            request()->routeIs('transaksi.parkir.index') ||
            (request()->route('status') === 'masuk') ||
            (request()->query('status') === 'masuk')
        ) {
            $items = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
                ->where('status', 'masuk')
                ->whereNull('waktu_keluar')
                ->orderBy('waktu_masuk', 'desc')
                ->paginate(15);
            return view('parkir.index', ['transaksis' => $items]);
        }

        $query = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
            ->where('status', 'keluar')
            ->where('status_pembayaran', 'berhasil');

        if (request()->filled('q')) {
            $q = request('q');
            $query->whereHas('kendaraan', function($sub) use ($q) {
                $sub->where('plat_nomor', 'like', '%' . $q . '%');
            });
        }

        if (request()->filled('tanggal_dari')) {
            $query->whereDate('waktu_keluar', '>=', request('tanggal_dari'));
        }
        if (request()->filled('tanggal_sampai')) {
            $query->whereDate('waktu_keluar', '<=', request('tanggal_sampai'));
        }

        if (request()->filled('id_area')) {
            $query->where('id_area', request('id_area'));
        }

        $items = $query->orderBy('waktu_keluar', 'desc')->paginate(15);
        return view('transaksi.index', ['transaksis' => $items]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kendaraan' => 'required|exists:tb_kendaraan,id_kendaraan',
            'waktu_masuk' => 'required|date',
            'id_tarif' => 'required|exists:tb_tarif,id_tarif',
            'id_user' => 'required|exists:tb_user,id',
            'id_area' => 'required|exists:tb_area_parkir,id_area',
            'status' => 'required|in:masuk,keluar',
            'catatan' => 'nullable|string|max:255',
        ]);

        Transaksi::create($data);
        return redirect()->route('transaksi.index')->with('success','Transaksi manual berhasil dibuat');
    }

    public function create()
    {
        $kendaraans = Kendaraan::orderBy('plat_nomor')->get();
        $tarifs = Tarif::orderBy('jenis_kendaraan')->get();
        $users = User::orderBy('name')->get();
        $areas = AreaParkir::orderBy('nama_area')->get();
        return view('transaksi.create', compact('kendaraans','tarifs','users','areas'));
    }

    public function show($id)
    {
        $item = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])->findOrFail($id);
        return view('transaksi.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Transaksi::findOrFail($id);
        $kendaraans = Kendaraan::orderBy('plat_nomor')->get();
        $tarifs = Tarif::orderBy('jenis_kendaraan')->get();
        $users = User::orderBy('name')->get();
        $areas = AreaParkir::orderBy('nama_area')->get();
        return view('transaksi.edit', compact('item','kendaraans','tarifs','users','areas'));
    }

    public function update(Request $request, $id)
    {
        $item = Transaksi::findOrFail($id);
        $data = $request->validate([
            'waktu_keluar' => 'nullable|date',
            'durasi_jam' => 'nullable|integer',
            'biaya_total' => 'nullable|numeric',
            'status' => 'required|in:masuk,keluar',
        ]);

        $item->update($data);
        return redirect()->route('transaksi.index')->with('success','Transaksi manual berhasil diupdate');
    }

    public function print($id)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])->findOrFail($id);
        return view('parkir.receipt', compact('transaksi'));
    }

    public function destroy($id)
    {
        Transaksi::destroy($id);
        return redirect()->route('transaksi.index')->with('success','Transaksi berhasil dihapus');
    }

    /**
     * Normalisasi plat nomor: uppercase, hapus spasi
     */
    private function normalizePlatNomor(string $plat): string
    {
        return strtoupper(str_replace(' ', '', trim($plat)));
    }
}
