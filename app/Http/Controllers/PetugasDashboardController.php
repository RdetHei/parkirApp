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
    public const SESSION_OPERATIONAL_AREA = 'operational_area_id';

    public function setOperationalArea(Request $request)
    {
        $request->validate([
            'kode_peta' => 'required|string|max:50',
        ]);

        $area = AreaParkir::findByMapCode($request->kode_peta);
        if (! $area) {
            $message = 'Kode peta tidak valid. Gunakan Kode Peta yang sama dengan di menu Area Parkir.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['kode_peta' => $message])->withInput();
        }

        session([self::SESSION_OPERATIONAL_AREA => $area->id_area]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Area aktif: ' . $area->nama_area,
                'area' => [
                    'nama' => $area->nama_area,
                    'map_code' => $area->map_code,
                ],
            ]);
        }

        return back()->with('success', 'Area tugas diaktifkan: ' . $area->nama_area);
    }

    public function clearOperationalArea(Request $request)
    {
        session()->forget(self::SESSION_OPERATIONAL_AREA);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Area tugas dihapus dari sesi.']);
        }

        return back()->with('success', 'Sesi area tugas dibersihkan. Masukkan kode peta untuk shift berikutnya.');
    }

    public function index()
    {
        try {
            $idArea = session(self::SESSION_OPERATIONAL_AREA);

            if (! $idArea) {
                return view('petugas.dashboard', [
                    'title' => 'Dashboard Petugas',
                    'needsOperationalArea' => true,
                    'operationalArea' => null,
                    'transaksiAktif' => 0,
                    'bookingAktif' => 0,
                    'transaksiHariIni' => 0,
                    'pendapatanHariIni' => 0,
                    'totalKapasitas' => 0,
                    'totalTerisi' => 0,
                    'aktivitasTerbaru' => collect([]),
                    'areaParkir' => AreaParkir::all(),
                    'area' => null,
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
                'needsOperationalArea' => false,
                'operationalArea' => $area,
                'transaksiAktif' => $transaksiAktif,
                'bookingAktif' => $bookingAktif,
                'transaksiHariIni' => $stats['transaksiHariIni'],
                'pendapatanHariIni' => $stats['pendapatanHariIni'],
                'totalKapasitas' => $totalKapasitas,
                'totalTerisi' => $totalTerisi,
                'aktivitasTerbaru' => $aktivitasTerbaru,
                'areaParkir' => AreaParkir::all(),
                'area' => $area,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PetugasDashboardController@index error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
