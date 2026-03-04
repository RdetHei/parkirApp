<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\AreaParkir;
use App\Models\User;
use App\Models\LogAktivitas;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Utama
        $totalKendaraan = Kendaraan::count();
        $totalTransaksi = Transaksi::count();
        $transaksiAktif = Transaksi::where('status', 'masuk')->count();
        $totalPendapatan = Pembayaran::where('status', 'berhasil')->sum('nominal');
        $pendapatanHariIni = Pembayaran::where('status', 'berhasil')
            ->whereDate('waktu_pembayaran', Carbon::today())
            ->sum('nominal');

        $pembayaranPending = Pembayaran::where('status', 'pending')->count();
        $totalUser = User::count();
        $transaksiHariIni = Transaksi::whereDate('waktu_masuk', Carbon::today())->count();

        // Data Area Parkir
        $areaParkir = AreaParkir::all();
        $totalKapasitas = $areaParkir->sum('kapasitas');
        $totalTerisi = $areaParkir->sum('terisi');

        // Data Grafik: Pendapatan 7 Hari Terakhir
        $grafikPendapatan = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $label = $date->translatedFormat('d M');
            $value = Pembayaran::where('status', 'berhasil')
                ->whereDate('waktu_pembayaran', $date)
                ->sum('nominal');
            $grafikPendapatan['labels'][] = $label;
            $grafikPendapatan['data'][] = $value;
        }

        // Data Grafik: Jenis Kendaraan (Pie)
        $grafikKendaraan = [
            'labels' => ['Mobil', 'Motor'],
            'data' => [
                Kendaraan::where('jenis_kendaraan', 'mobil')->count(),
                Kendaraan::where('jenis_kendaraan', 'motor')->count(),
            ]
        ];

        // Aktivitas Terbaru (Log Sistem)
        $aktivitasTerbaru = Transaksi::with(['kendaraan', 'area', 'user'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $title = 'Admin Dashboard';

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
            'totalUser',
            'areaParkir',
            'grafikPendapatan',
            'grafikKendaraan',
            'aktivitasTerbaru'
        ));
    }
}
