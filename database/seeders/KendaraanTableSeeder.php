<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use Illuminate\Database\Seeder;

class KendaraanTableSeeder extends Seeder
{
    public function run(): void
    {
        Kendaraan::query()->delete();

        Kendaraan::insert([
            [
                'id_kendaraan' => 1,
                'plat_nomor' => 'B1234CD',
                'jenis_kendaraan' => 'mobil',
                'warna' => 'Hitam',
                'pemilik' => 'Andi',
                'id_user' => 1,
                'created_at' => '2026-02-20 04:13:01',
                'updated_at' => '2026-02-20 04:13:01',
                'deleted_at' => null,
            ],
            [
                'id_kendaraan' => 2,
                'plat_nomor' => 'D4321XY',
                'jenis_kendaraan' => 'motor',
                'warna' => 'Merah',
                'pemilik' => 'Budi',
                'id_user' => 1,
                'created_at' => '2026-02-20 04:13:02',
                'updated_at' => '2026-02-20 04:13:02',
                'deleted_at' => null,
            ],
            [
                'id_kendaraan' => 3,
                'plat_nomor' => 'F5555ZZ',
                'jenis_kendaraan' => 'lainnya',
                'warna' => 'Putih',
                'pemilik' => 'Citra',
                'id_user' => 1,
                'created_at' => '2026-02-20 04:13:02',
                'updated_at' => '2026-02-20 04:13:02',
                'deleted_at' => null,
            ],
        ]);
    }
}

