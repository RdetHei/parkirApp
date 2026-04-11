<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\AreaParkir;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PetugasDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $idArea = $user->id_area;

            if (! $idArea && $user->role === 'petugas') {
                return view('petugas.dashboard', [
                    'title' => 'Dashboard Petugas',
                    'error' => 'Anda belum ditugaskan ke area manapun. Silakan hubungi Admin.',
                    'transaksiAktif' => 0,
                    'bookingAktif' => 0,
                    'transaksiHariIni' => 0,
                    'pendapatanHariIni' => 0,
                    'totalKapasitas' => 0,
                    'totalTerisi' => 0,
                    'aktivitasTerbaru' => collect([]),
                    'areaParkir' => AreaParkir::all(),
                    'area' => null
                ]);
            }

            // Stats cached per area
            $cacheKey = "petugas_dashboard_stats_{$idArea}";
            $stats = Cache::remember($cacheKey, 60, function() use ($idArea) {
                $query = Transaksi::where('id_area', $idArea);

                return [
                    'transaksiHariIni' => (clone $query)->whereDate('waktu_masuk', \Illuminate\Support\Carbon::today())->count(),
                    'pendapatanHariIni' => (clone $query)->where('status', 'keluar')
                        ->where('status_pembayaran', 'berhasil')
                        ->whereDate('waktu_keluar', \Illuminate\Support\Carbon::today())
                        ->sum('biaya_total'),
                ];
            });

            // Live counts filtered by area
            $transaksiAktif = Transaksi::where('id_area', $idArea)->where('status', 'masuk')->count();
            $bookingAktif = Transaksi::where('id_area', $idArea)
                ->where('status', 'bookmarked')
                ->where('bookmarked_at', '>', \Illuminate\Support\Carbon::now()->subMinutes(10))
                ->count();

            // Kapasitas parkir - Optimized direct DB aggregation
            $area = AreaParkir::find($idArea);
            $totalKapasitas = $area->kapasitas ?? 0;
            $totalTerisi = $area->terisi ?? 0;

            // Aktivitas terbaru - Eager Loading & Limit
            $aktivitasTerbaru = Transaksi::with(['kendaraan', 'area'])
                ->where('id_area', $idArea)
                ->whereIn('status', ['masuk', 'keluar', 'bookmarked'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $title = 'Dashboard Petugas';

            return view('petugas.dashboard', [
                'title' => $title,
                'transaksiAktif' => $transaksiAktif,
                'bookingAktif' => $bookingAktif,
                'transaksiHariIni' => $stats['transaksiHariIni'],
                'pendapatanHariIni' => $stats['pendapatanHariIni'],
                'totalKapasitas' => $totalKapasitas,
                'totalTerisi' => $totalTerisi,
                'aktivitasTerbaru' => $aktivitasTerbaru,
                'areaParkir' => AreaParkir::all(),
                'area' => $area
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PetugasDashboardController@index error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
