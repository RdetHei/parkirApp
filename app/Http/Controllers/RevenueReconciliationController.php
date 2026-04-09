<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class RevenueReconciliationController extends Controller
{
    public function index()
    {
        $totalFromTransaksi = (float) Transaksi::where('status', 'keluar')
            ->where('status_pembayaran', 'berhasil')
            ->sum('biaya_total');

        $totalFromPembayaran = (float) Pembayaran::where('status', 'berhasil')->sum('nominal');

        $missing = Transaksi::query()
            ->where('status', 'keluar')
            ->where('status_pembayaran', 'berhasil')
            ->where(function ($q) {
                $q->whereNull('id_pembayaran')
                    ->orWhereDoesntHave('pembayaran');
            })
            ->with(['user', 'kendaraan'])
            ->orderByDesc('waktu_keluar')
            ->paginate(20);

        return view('admin.reconciliation.revenue', [
            'title' => 'Rekonsiliasi Pendapatan',
            'totalFromTransaksi' => $totalFromTransaksi,
            'totalFromPembayaran' => $totalFromPembayaran,
            'delta' => $totalFromTransaksi - $totalFromPembayaran,
            'missing' => $missing,
        ]);
    }

    public function syncMissingPayments(): RedirectResponse
    {
        $synced = 0;

        DB::transaction(function () use (&$synced) {
            $rows = Transaksi::query()
                ->where('status', 'keluar')
                ->where('status_pembayaran', 'berhasil')
                ->where(function ($q) {
                    $q->whereNull('id_pembayaran')
                        ->orWhereDoesntHave('pembayaran');
                })
                ->lockForUpdate()
                ->get();

            foreach ($rows as $tx) {
                $existing = $tx->pembayaran;
                if ($existing) {
                    $tx->update(['id_pembayaran' => $existing->id_pembayaran]);
                    continue;
                }

                $p = Pembayaran::create([
                    'id_parkir' => $tx->id_parkir,
                    'nominal' => (float) ($tx->biaya_total ?? 0),
                    'metode' => 'system-sync',
                    'status' => 'berhasil',
                    'id_user' => null,
                    'keterangan' => 'Sinkronisasi otomatis dari transaksi berhasil.',
                    'waktu_pembayaran' => $tx->waktu_keluar ?? now(),
                ]);

                $tx->update(['id_pembayaran' => $p->id_pembayaran]);
                $synced++;
            }
        });

        return back()->with('success', "Sinkronisasi selesai. {$synced} pembayaran berhasil dipulihkan.");
    }
}

