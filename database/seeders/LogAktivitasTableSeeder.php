<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogAktivitasTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_log_aktivitas')->delete();

        DB::table('tb_log_aktivitas')->insert([
            [
                'id_log' => 1,
                'id_user' => 1,
                'aktivitas' => 'Seeder: Membuat data awal area, tarif, kendaraan, transaksi, pembayaran',
                'waktu_aktivitas' => '2026-02-20 11:13:07',
                'created_at' => '2026-02-20 04:13:07',
                'updated_at' => '2026-02-20 04:13:07',
            ],
            [
                'id_log' => 2,
                'id_user' => 1,
                'aktivitas' => 'Seeder: Contoh log aktivitas operator',
                'waktu_aktivitas' => '2026-02-20 11:03:07',
                'created_at' => '2026-02-20 04:13:07',
                'updated_at' => '2026-02-20 04:13:07',
            ],
            [
                'id_log' => 3,
                'id_user' => 3,
                'aktivitas' => 'Login ke sistem - Petugas (Petugas123)',
                'waktu_aktivitas' => '2026-02-20 11:15:50',
                'created_at' => '2026-02-20 04:15:50',
                'updated_at' => '2026-02-20 04:15:50',
            ],
            [
                'id_log' => 4,
                'id_user' => 2,
                'aktivitas' => 'Login ke sistem - Rudi (Admin)',
                'waktu_aktivitas' => '2026-02-22 21:25:41',
                'created_at' => '2026-02-22 14:25:41',
                'updated_at' => '2026-02-22 14:25:41',
            ],
            [
                'id_log' => 5,
                'id_user' => 2,
                'aktivitas' => 'Logout - Rudi (Admin)',
                'waktu_aktivitas' => '2026-02-22 21:28:34',
                'created_at' => '2026-02-22 14:28:34',
                'updated_at' => '2026-02-22 14:28:34',
            ],
            [
                'id_log' => 6,
                'id_user' => 3,
                'aktivitas' => 'Login ke sistem - Petugas (Petugas)',
                'waktu_aktivitas' => '2026-02-22 21:28:50',
                'created_at' => '2026-02-22 14:28:50',
                'updated_at' => '2026-02-22 14:28:50',
            ],
            [
                'id_log' => 7,
                'id_user' => 3,
                'aktivitas' => 'Mencatat kendaraan keluar parkir #00000001',
                'waktu_aktivitas' => '2026-02-22 22:00:36',
                'created_at' => '2026-02-22 15:00:36',
                'updated_at' => '2026-02-22 15:00:36',
            ],
            [
                'id_log' => 8,
                'id_user' => 3,
                'aktivitas' => 'Mengupdate transaksi parkir #00000002',
                'waktu_aktivitas' => '2026-02-22 22:01:49',
                'created_at' => '2026-02-22 15:01:49',
                'updated_at' => '2026-02-22 15:01:49',
            ],
            [
                'id_log' => 9,
                'id_user' => 3,
                'aktivitas' => 'Mengupdate transaksi parkir #00000002',
                'waktu_aktivitas' => '2026-02-22 22:02:30',
                'created_at' => '2026-02-22 15:02:30',
                'updated_at' => '2026-02-22 15:02:30',
            ],
            [
                'id_log' => 10,
                'id_user' => 3,
                'aktivitas' => 'Mengupdate transaksi parkir #00000002',
                'waktu_aktivitas' => '2026-02-22 22:02:38',
                'created_at' => '2026-02-22 15:02:38',
                'updated_at' => '2026-02-22 15:02:38',
            ],
            [
                'id_log' => 11,
                'id_user' => 3,
                'aktivitas' => 'Mengupdate transaksi parkir #00000002',
                'waktu_aktivitas' => '2026-02-22 22:03:48',
                'created_at' => '2026-02-22 15:03:48',
                'updated_at' => '2026-02-22 15:03:48',
            ],
        ]);
    }
}

