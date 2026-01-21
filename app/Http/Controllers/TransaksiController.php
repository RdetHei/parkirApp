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

class TransaksiController extends Controller
{
    // == ALUR UTAMA PARKIR ==

    /**
     * Menangani proses kendaraan masuk (check-in).
     * Logika ini dipindahkan dari ParkirController@store
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'id_kendaraan' => 'required|exists:tb_kendaraan,id_kendaraan',
            'id_tarif' => 'required|exists:tb_tarif,id_tarif',
            'id_area' => 'required|exists:tb_area_parkir,id_area',
        ]);

        try {
            $area = AreaParkir::findOrFail($request->id_area);

            // Cek kapasitas
            if ($area->terisi >= $area->kapasitas) {
                return back()->with('error', 'Kapasitas area parkir sudah penuh');
            }

            $transaksi = Transaksi::create([
                'id_kendaraan' => $request->id_kendaraan,
                'id_tarif' => $request->id_tarif,
                'id_area' => $request->id_area,
                'id_user' => Auth::id(),
                'waktu_masuk' => Carbon::now(),
                'status' => 'masuk',
            ]);

            // Update kapasitas area parkir
            $area->increment('terisi');

            return redirect()->route('transaksi.index') // Arahkan ke daftar transaksi aktif
                ->with('success', 'Kendaraan berhasil dicatat masuk parkir.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menangani proses kendaraan keluar (check-out).
     * Logika ini dipindahkan dari ParkirController@update
     */
    public function checkOut(Request $request, $id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);

            if ($transaksi->status === 'keluar') {
                return back()->with('error', 'Kendaraan ini sudah tercatat keluar.');
            }

            // Kalkulasi durasi dan biaya
            $waktu_keluar = Carbon::now();
            $durasi_jam = ceil($waktu_keluar->diffInMinutes($transaksi->waktu_masuk) / 60);
            $biaya_total = $durasi_jam * $transaksi->tarif->tarif_perjam;

            $transaksi->update([
                'waktu_keluar' => $waktu_keluar,
                'durasi_jam' => $durasi_jam,
                'biaya_total' => $biaya_total,
                'status' => 'keluar',
            ]);

            // Kurangi kapasitas terisi di area parkir
            $area = AreaParkir::findOrFail($transaksi->id_area);
            if ($area->terisi > 0) {
                $area->decrement('terisi');
            }

            // Arahkan ke halaman pembuatan pembayaran
            return redirect()->route('payment.create', $transaksi->id_parkir)
                ->with('success', 'Kendaraan berhasil checkout. Silakan lanjut ke pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update transaksi: ' . $e->getMessage());
        }
    }

    // == CRUD UNTUK ADMIN ==

    public function index()
    {
        $items = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
            ->orderBy('id_parkir','desc')
            ->paginate(15);

        // Filter untuk menampilkan view yang berbeda (opsional)
        if (request()->has('status') && request('status') == 'masuk') {
            $items = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
                ->where('status', 'masuk')
                ->orderBy('waktu_masuk', 'desc')
                ->paginate(15);
            return view('parkir.index', ['transaksis' => $items]); // Menggunakan view parkir.index untuk transaksi aktif
        }

        return view('transaksi.index', compact('items'));
    }

    public function create()
    {
        $kendaraans = Kendaraan::orderBy('plat_nomor')->get();
        $tarifs = Tarif::orderBy('jenis_kendaraan')->get();
        $users = User::orderBy('name')->get();
        $areas = AreaParkir::orderBy('nama_area')->get();
        return view('transaksi.create', compact('kendaraans','tarifs','users','areas'));
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
        ]);

        Transaksi::create($data);
        return redirect()->route('transaksi.index')->with('success','Transaksi manual berhasil dibuat');
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

