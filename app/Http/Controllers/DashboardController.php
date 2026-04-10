<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\AreaParkir;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Added

class DashboardController extends Controller
{
    public function index()
    {
        try {
            \Illuminate\Support\Facades\Log::info('DashboardController@index hit');

            // Added user and area filtering logic
            $user = Auth::user();
            $areaId = null;
            $areaName = 'Semua Area';

            if ($user->role === 'petugas' && $user->id_area) {
                $areaId = $user->id_area;
                $areaName = $user->area->nama_area ?? 'Area Tidak Dikenal';
            }

            // Statistik Utama
            $totalKendaraan = Kendaraan::count(); // Global, no area filter

            $totalTransaksiQuery = Transaksi::query();
            if ($areaId) {
                $totalTransaksiQuery->where('id_area', $areaId);
            }
            $totalTransaksi = $totalTransaksiQuery->count(); // Modified

            $transaksiAktifQuery = Transaksi::where('status', 'masuk');
            if ($areaId) {
                $transaksiAktifQuery->where('id_area', $areaId);
            }
            $transaksiAktif = $transaksiAktifQuery->count(); // Modified

            $totalPendapatanQuery = Transaksi::where('status', 'keluar')
                ->where('status_pembayaran', 'berhasil');
            if ($areaId) {
                $totalPendapatanQuery->where('id_area', $areaId);
            }
            $totalPendapatan = $totalPendapatanQuery->sum('biaya_total'); // Modified

            $pendapatanHariIniQuery = Transaksi::where('status', 'keluar')
                ->where('status_pembayaran', 'berhasil')
                ->whereDate('waktu_keluar', Carbon::today());
            if ($areaId) {
                $pendapatanHariIniQuery->where('id_area', $areaId);
            }
            $pendapatanHariIni = $pendapatanHariIniQuery->sum('biaya_total'); // Modified

            $pembayaranPending = Pembayaran::where('status', 'pending')->count(); // Global, no area filter
            $totalUser = User::count(); // Global, no area filter

            $transaksiHariIniQuery = Transaksi::whereDate('waktu_masuk', Carbon::today());
            if ($areaId) {
                $transaksiHariIniQuery->where('id_area', $areaId);
            }
            $transaksiHariIni = $transaksiHariIniQuery->count(); // Modified

            // Data Area Parkir
            $areaParkir = AreaParkir::all(); // All areas, no filter
            $totalKapasitas = $areaParkir->sum('kapasitas'); // Global, no filter
            $totalTerisi = $areaParkir->sum('terisi'); // Global, no filter

            // Data Grafik: Pendapatan 7 Hari Terakhir
            $grafikPendapatan = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $label = $date->translatedFormat('d M');
                $valueQuery = Transaksi::where('status', 'keluar')
                    ->where('status_pembayaran', 'berhasil')
                    ->whereDate('waktu_keluar', $date);
                if ($areaId) {
                    $valueQuery->where('id_area', $areaId);
                }
                $value = $valueQuery->sum('biaya_total'); // Modified
                $grafikPendapatan['labels'][] = $label;
                $grafikPendapatan['data'][] = $value;
            }

            // Analisis Jam Sibuk (Kapan kendaraan paling banyak masuk)
            $grafikJamSibuk = [
                'labels' => [],
                'data' => []
            ];
            for ($h = 0; $h < 24; $h++) {
                $jamSibukQuery = Transaksi::whereRaw('HOUR(waktu_masuk) = ?', [$h])
                    ->whereDate('waktu_masuk', '>=', Carbon::today()->subDays(30)); // Rata-rata 30 hari terakhir
                if ($areaId) {
                    $jamSibukQuery->where('id_area', $areaId);
                }
                $grafikJamSibuk['labels'][] = sprintf('%02d:00', $h);
                $grafikJamSibuk['data'][] = $jamSibukQuery->count() / 30; // Rata-rata per hari // Modified
            }

            // Analisis Pendapatan Per Jam (Heatmap Data)
            $revenueByHour = [];
            for ($h = 0; $h < 24; $h++) {
                $revenueHourQuery = Transaksi::where('status', 'keluar')
                    ->where('status_pembayaran', 'berhasil')
                    ->whereRaw('HOUR(waktu_keluar) = ?', [$h]);
                if ($areaId) {
                    $revenueHourQuery->where('id_area', $areaId);
                }
                $revenueByHour[$h] = $revenueHourQuery->sum('biaya_total'); // Modified
            }

            // Analisis Area Terfavorit (Berdasarkan jumlah transaksi)
            $topAreasQuery = Transaksi::select('id_area', DB::raw('count(*) as total'));
            if ($areaId) {
                $topAreasQuery->where('id_area', $areaId);
            }
            $topAreas = $topAreasQuery->groupBy('id_area')
                ->with('area')
                ->orderByDesc('total')
                ->limit(5)
                ->get(); // Modified

            // Data Grafik: Jenis Kendaraan (Pie)
            $grafikKendaraan = [
                'labels' => ['Motor', 'Mobil', 'Lainnya'], // Changed from original
                'data' => [
                    Kendaraan::where('jenis_kendaraan', 'motor')->count(), // Changed from original
                    Kendaraan::where('jenis_kendaraan', 'mobil')->count(), // Changed from original
                    Kendaraan::whereNotIn('jenis_kendaraan', ['motor', 'mobil'])->count(), // Added
                ]
            ];

            // Aktivitas Terbaru (Log Sistem)
            $aktivitasTerbaruQuery = Transaksi::with(['kendaraan', 'area', 'user']);
            if ($areaId) {
                $aktivitasTerbaruQuery->where('id_area', $areaId);
            }
            $aktivitasTerbaru = $aktivitasTerbaruQuery->orderByDesc('created_at')
                ->limit(5)
                ->get(); // Modified

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
                    'topAreas',
                    'areaName' // Added
                ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('DashboardController@index error: ' . $e->getMessage());
                \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
                return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
            }
    }
}
