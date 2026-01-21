<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LogAktifitas;
use Carbon\Carbon;

class LogAktifitasSeeder extends Seeder
{
    public function run(): void
    {
        LogAktifitas::create([
            'id_user' => 1,
            'aktivitas' => 'Seeder: Membuat data awal area, tarif, kendaraan, transaksi, pembayaran',
            'waktu_aktivitas' => Carbon::now(),
        ]);

        LogAktifitas::create([
            'id_user' => 1,
            'aktivitas' => 'Seeder: Contoh log aktivitas operator',
            'waktu_aktivitas' => Carbon::now()->subMinutes(10),
        ]);
    }
}
