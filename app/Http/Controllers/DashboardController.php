<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\AreaParkir;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            \Illuminate\Support\Facades\Log::info('DashboardController@index hit');
            // Statistik Utama
            $totalKendaraan = Kendaraan::count();
        $totalTransaksi = Transaksi::count();
        $transaksiAktif = Transaksi::where('status', 'masuk')->count();
        $totalPendapatan = Transaksi::where('status', 'keluar')
            ->where('status_pembayaran', 'berhasil')
            ->sum('biaya_total');
        $pendapatanHariIni = Transaksi::where('status', 'keluar')
            ->where('status_pembayaran', 'berhasil')
            ->whereDate('waktu_keluar', Carbon::today())
            ->sum('biaya_total');

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
            $value = Transaksi::where('status', 'keluar')
                ->where('status_pembayaran', 'berhasil')
                ->whereDate('waktu_keluar', $date)
                ->sum('biaya_total');
            $grafikPendapatan['labels'][] = $label;
            $grafikPendapatan['data'][] = $value;
        }

        // Analisis Jam Sibuk (Kapan kendaraan paling banyak masuk)
        $grafikJamSibuk = [
            'labels' => [],
            'data' => []
        ];
        for ($h = 0; $h < 24; $h++) {
            $grafikJamSibuk['labels'][] = sprintf('%02d:00', $h);
            $grafikJamSibuk['data'][] = Transaksi::whereRaw('HOUR(waktu_masuk) = ?', [$h])
                ->whereDate('waktu_masuk', '>=', Carbon::today()->subDays(30)) // Rata-rata 30 hari terakhir
                ->count() / 30; // Rata-rata per hari
        }

        // Analisis Pendapatan Per Jam (Heatmap Data)
        $revenueByHour = [];
        for ($h = 0; $h < 24; $h++) {
            $revenueByHour[$h] = Transaksi::where('status', 'keluar')
                ->where('status_pembayaran', 'berhasil')
                ->whereRaw('HOUR(waktu_keluar) = ?', [$h])
                ->sum('biaya_total');
        }

        // Analisis Area Terfavorit (Berdasarkan jumlah transaksi)
        $topAreas = Transaksi::select('id_area', DB::raw('count(*) as total'))
            ->groupBy('id_area')
            ->with('area')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

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
                'grafikJamSibuk',
                'grafikKendaraan',
                'aktivitasTerbaru',
                'revenueByHour',
                'topAreas'
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DashboardController@index error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
