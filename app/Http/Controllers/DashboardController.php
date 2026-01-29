<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\AreaParkir;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil statistik real dari database
        $totalKendaraan = Kendaraan::count();

        // Total transaksi (semua status)
        $totalTransaksi = Transaksi::count();

        // Transaksi aktif (belum keluar)
        $transaksiAktif = Transaksi::where('status', 'masuk')->count();

        // Total pendapatan dari pembayaran yang sudah berhasil
        $totalPendapatan = Pembayaran::where('status', 'berhasil')
            ->sum('nominal');

        // Pendapatan hari ini
        $pendapatanHariIni = Pembayaran::where('status', 'berhasil')
            ->whereDate('created_at', Carbon::today())
            ->sum('nominal');

        // Area parkir dengan kapasitas tertinggi
        $areaParkir = AreaParkir::all();
        $totalKapasitas = $areaParkir->sum('kapasitas');
        $totalTerisi = $areaParkir->sum('terisi');

        // Status pembayaran pending
        $pembayaranPending = Pembayaran::where('status', 'pending')->count();

        // Transaksi hari ini
        $transaksiHariIni = Transaksi::whereDate('waktu_masuk', Carbon::today())->count();

        $title = 'Dashboard';

        return view('dashboard', compact(
            'title',
            'totalKendaraan',
            'totalTransaksi',
            'transaksiAktif',
            'totalPendapatan',
            'pendapatanHariIni',
            'totalKapasitas',
            'totalTerisi',
            'pembayaranPending',
            'transaksiHariIni',
            'areaParkir'
        ));
    }
}
