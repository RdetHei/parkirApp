<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\AreaParkir;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = $request->query('days', 7);
        if (!in_array($days, [7, 30, 90])) {
            $days = 7;
        }

        $totalPendapatan = Transaksi::where('status', 'keluar')
            ->where('status_pembayaran', 'berhasil')
            ->sum('biaya_total');
        $pendapatanHariIni = Transaksi::where('status', 'keluar')
            ->where('status_pembayaran', 'berhasil')
            ->whereDate('waktu_keluar', Carbon::today())
            ->sum('biaya_total');
        $transaksiBerhasil = Transaksi::where('status', 'keluar')->where('status_pembayaran', 'berhasil')->count();
        $pembayaranPending = Pembayaran::pending()->count();
        $areaParkir = AreaParkir::all();
        $totalKapasitas = $areaParkir->sum('kapasitas');
        $totalTerisi = $areaParkir->sum('terisi');

        $harian = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            $harian[] = [
                'label' => $tanggal->format('d/m'),
                'nominal' => Transaksi::where('status', 'keluar')
                    ->where('status_pembayaran', 'berhasil')
                    ->whereDate('waktu_keluar', $tanggal)
                    ->sum('biaya_total'),
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
            'harian',
            'days'
        ));
    }
}
