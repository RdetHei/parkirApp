<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\AreaParkir;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $totalPendapatan = Pembayaran::berhasil()->sum('nominal');
        $pendapatanHariIni = Pembayaran::berhasil()->whereDate('created_at', Carbon::today())->sum('nominal');
        $transaksiBerhasil = Transaksi::where('status', 'keluar')->where('status_pembayaran', 'berhasil')->count();
        $pembayaranPending = Pembayaran::pending()->count();
        $areaParkir = AreaParkir::all();
        $totalKapasitas = $areaParkir->sum('kapasitas');
        $totalTerisi = $areaParkir->sum('terisi');

        $harian = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            $harian[] = [
                'label' => $tanggal->format('d/m'),
                'nominal' => Pembayaran::berhasil()->whereDate('created_at', $tanggal)->sum('nominal'),
                'transaksi' => Transaksi::where('status', 'keluar')->where('status_pembayaran', 'berhasil')->whereDate('waktu_keluar', $tanggal)->count(),
            ];
        }

        return view('owner.dashboard', compact(
            'totalPendapatan',
            'pendapatanHariIni',
            'transaksiBerhasil',
            'pembayaranPending',
            'totalKapasitas',
            'totalTerisi',
            'areaParkir',
            'harian'
        ));
    }
}
