<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\AreaParkir;
use App\Models\Pembayaran;
use Carbon\Carbon;

class PetugasDashboardController extends Controller
{
    public function index()
    {
        try {
            \Illuminate\Support\Facades\Log::info('PetugasDashboardController@index hit');
            // Transaksi aktif (belum keluar)
            $transaksiAktif = Transaksi::where('status', 'masuk')->count();

            // Transaksi hari ini (masuk hari ini)
            $transaksiHariIni = Transaksi::whereDate('waktu_masuk', Carbon::today())->count();

            // Pendapatan hari ini
            $pendapatanHariIni = Pembayaran::where('status', 'berhasil')
                ->whereDate('waktu_pembayaran', Carbon::today())
                ->sum('nominal');

            // Kapasitas parkir
            $areaParkir = AreaParkir::all();
            $totalKapasitas = $areaParkir->sum('kapasitas');
            $totalTerisi = $areaParkir->sum('terisi');

            // Aktivitas terbaru (5 transaksi terakhir: masuk atau keluar)
            $aktivitasTerbaru = Transaksi::with(['kendaraan', 'area'])
                ->whereIn('status', ['masuk', 'keluar'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $title = 'Dashboard Petugas';

            return view('petugas.dashboard', compact(
                'title',
                'transaksiAktif',
                'transaksiHariIni',
                'pendapatanHariIni',
                'totalKapasitas',
                'totalTerisi',
                'aktivitasTerbaru',
                'areaParkir'
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PetugasDashboardController@index error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
