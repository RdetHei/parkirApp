<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        // Create pembayaran for transaksi that were marked as paid
        $paidTx = Transaksi::where('status_pembayaran','sudah_bayar')->get();
        foreach ($paidTx as $tx) {
            $p = Pembayaran::create([
                'id_parkir' => $tx->id_parkir,
                'nominal' => $tx->biaya_total ?? 0,
                'metode' => 'qr_scan',
                'status' => 'berhasil',
                'keterangan' => 'Seeded payment',
                'id_user' => $tx->id_user,
                'waktu_pembayaran' => Carbon::now()->subHours(5),
            ]);

            // link back to transaksi
            $tx->update(['id_pembayaran' => $p->id_pembayaran]);
        }

        // Optionally create a pending payment for a specific transaction
        $pendingTx = Transaksi::where('status_pembayaran','belum_bayar')->first();
        if ($pendingTx) {
            Pembayaran::create([
                'id_parkir' => $pendingTx->id_parkir,
                'nominal' => $pendingTx->biaya_total ?? 0,
                'metode' => 'manual',
                'status' => 'pending',
                'keterangan' => 'Menunggu konfirmasi petugas',
                'id_user' => 1,
                'waktu_pembayaran' => null,
            ]);
        }
    }
}
