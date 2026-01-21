<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kendaraan;
use Carbon\Carbon;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        // sample vehicles linked to user id 1
        Kendaraan::create([
            'plat_nomor' => 'B1234CD',
            'jenis_kendaraan' => 'mobil',
            'warna' => 'Hitam',
            'pemilik' => 'Andi',
            'id_user' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Kendaraan::create([
            'plat_nomor' => 'D4321XY',
            'jenis_kendaraan' => 'motor',
            'warna' => 'Merah',
            'pemilik' => 'Budi',
            'id_user' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Kendaraan::create([
            'plat_nomor' => 'F5555ZZ',
            'jenis_kendaraan' => 'lainnya',
            'warna' => 'Putih',
            'pemilik' => 'Citra',
            'id_user' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
