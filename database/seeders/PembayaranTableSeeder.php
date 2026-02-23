<?php

namespace Database\Seeders;

use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class PembayaranTableSeeder extends Seeder
{
    public function run(): void
    {
        Pembayaran::query()->delete();

        Pembayaran::insert([
            [
                'id_pembayaran' => 3,
                'id_parkir' => 2,
                'order_id' => 'PARKIR-2-1771772558',
                'transaction_id' => '64ea254e-0627-4718-b6e7-a0195d017a4d',
                'payment_type' => 'bank_transfer',
                'nominal' => 8000,
                'metode' => 'midtrans',
                'status' => 'berhasil',
                'keterangan' => 'Pembayaran Midtrans (bank_transfer)',
                'id_user' => null,
                'waktu_pembayaran' => '2026-02-22 22:03:47',
                'created_at' => '2026-02-22 15:03:47',
                'updated_at' => '2026-02-22 15:03:47',
                'deleted_at' => null,
            ],
        ]);

        // Update relasi transaksi ke pembayaran sesuai dump
        Transaksi::where('id_parkir', 2)->update([
            'id_pembayaran' => 3,
            'status_pembayaran' => 'berhasil',
            'midtrans_order_id' => 'PARKIR-2-1771772558',
        ]);
    }
}

