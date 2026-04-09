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
            \Illuminate\Support\Facades\Log::info('PetugasDashboardController@index hit');

            // Non-live stats cached for 1 minute
            $stats = Cache::remember('petugas_dashboard_stats', 60, function() {
                return [
                    'transaksiHariIni' => Transaksi::whereDate('waktu_masuk', Carbon::today())->count(),
                    'pendapatanHariIni' => Transaksi::where('status', 'keluar')
                        ->where('status_pembayaran', 'berhasil')
                        ->whereDate('waktu_keluar', Carbon::today())
                        ->sum('biaya_total'),
                ];
            });

            // Live counts
            $transaksiAktif = Transaksi::where('status', 'masuk')->count();
            $bookingAktif = Transaksi::where('status', 'bookmarked')
                ->where('bookmarked_at', '>', Carbon::now()->subMinutes(10))
                ->count();

            // Kapasitas parkir - Optimized to one query
            $areaParkir = AreaParkir::all();
            $totalKapasitas = $areaParkir->sum('kapasitas');
            $totalTerisi = $areaParkir->sum('terisi');

            // Aktivitas terbaru
            $aktivitasTerbaru = Transaksi::with(['kendaraan', 'area'])
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
                'areaParkir' => $areaParkir
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PetugasDashboardController@index error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
