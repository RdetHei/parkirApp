<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class TransaksiTableSeeder extends Seeder
{
    public function run(): void
    {
        Transaksi::query()->delete();

        // Insert awal tanpa relasi ke pembayaran untuk menghindari masalah FK
        Transaksi::insert([
            [
                'id_parkir' => 1,
                'id_kendaraan' => 1,
                'waktu_masuk' => '2026-02-20 09:13:03',
                'waktu_keluar' => '2026-02-22 22:00:35',
                'id_tarif' => 2,
                'durasi_jam' => -60,
                'biaya_total' => -300000,
                'status' => 'keluar',
                'bookmarked_at' => null,
                'catatan' => null,
                'status_pembayaran' => 'pending',
                'id_pembayaran' => null,
                'midtrans_order_id' => null,
                'id_user' => 1,
                'id_area' => 1,
                'created_at' => '2026-02-20 02:13:03',
                'updated_at' => '2026-02-22 15:00:36',
                'deleted_at' => null,
            ],
            [
                'id_parkir' => 2,
                'id_kendaraan' => 2,
                'waktu_masuk' => '2026-02-20 06:13:04',
                'waktu_keluar' => '2026-02-20 10:13:04',
                'id_tarif' => 1,
                'durasi_jam' => 4,
                'biaya_total' => 8000,
                'status' => 'keluar',
                'bookmarked_at' => null,
                'catatan' => null,
                'status_pembayaran' => 'berhasil',
                'id_pembayaran' => null, // akan di-update setelah pembayaran dibuat
                'midtrans_order_id' => 'PARKIR-2-1771772558',
                'id_user' => 1,
                'id_area' => 1,
                'created_at' => '2026-02-19 23:13:04',
                'updated_at' => '2026-02-22 15:03:48',
                'deleted_at' => null,
            ],
            [
                'id_parkir' => 3,
                'id_kendaraan' => 3,
                'waktu_masuk' => '2026-02-20 01:13:04',
                'waktu_keluar' => '2026-02-20 05:13:04',
                'id_tarif' => 1,
                'durasi_jam' => 4,
                'biaya_total' => 8000,
                'status' => 'keluar',
                'bookmarked_at' => null,
                'catatan' => null,
                'status_pembayaran' => 'berhasil',
                'id_pembayaran' => null,
                'midtrans_order_id' => null,
                'id_user' => 1,
                'id_area' => 1,
                'created_at' => '2026-02-19 18:13:04',
                'updated_at' => '2026-02-20 04:13:05',
                'deleted_at' => null,
            ],
        ]);
    }
}

