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
     * Menggunakan transaction untuk mencegah race condition
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'id_kendaraan' => 'required|exists:tb_kendaraan,id_kendaraan',
            'id_tarif' => 'required|exists:tb_tarif,id_tarif',
            'id_area' => 'required|exists:tb_area_parkir,id_area',
            'catatan' => 'nullable|string|max:255',
        ]);

        try {
            // Use transaction untuk atomic operation
            $transaksi = DB::transaction(function () use ($request) {
                // Lock area parkir untuk prevent race condition
                $area = AreaParkir::lockForUpdate()->findOrFail($request->id_area);

                // Cek kapasitas dengan lock
                if ($area->terisi >= $area->kapasitas) {
                    throw new \Exception('Kapasitas area parkir sudah penuh');
                }

                // Buat transaksi
                $transaksi = Transaksi::create([
                    'id_kendaraan' => $request->id_kendaraan,
                    'id_tarif' => $request->id_tarif,
                    'id_area' => $request->id_area,
                    'id_user' => Auth::id(),
                    'waktu_masuk' => Carbon::now(),
                    'status' => 'masuk',
                    'catatan' => $request->catatan,
                ]);

                // Update kapasitas dengan atomic increment
                $area->increment('terisi');

                return $transaksi;
            });

            return redirect()->route('transaksi.index')
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
        // Filter untuk menampilkan view yang berbeda (opsional)
        if (request()->has('status') && request('status') == 'masuk') {
            $items = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
                ->where('status', 'masuk')
                ->orderBy('waktu_masuk', 'desc')
                ->paginate(15);
            return view('parkir.index', ['transaksis' => $items]);
        }

        $items = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
            ->orderBy('id_parkir','desc')
            ->paginate(15);
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
}

