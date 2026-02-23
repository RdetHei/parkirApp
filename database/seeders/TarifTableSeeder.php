<?php

namespace Database\Seeders;

use App\Models\Tarif;
use Illuminate\Database\Seeder;

class TarifTableSeeder extends Seeder
{
    public function run(): void
    {
        Tarif::query()->delete();

        Tarif::insert([
            [
                'id_tarif' => 1,
                'jenis_kendaraan' => 'motor',
                'tarif_perjam' => 2000,
                'created_at' => '2026-02-20 04:12:59',
                'updated_at' => '2026-02-20 04:12:59',
            ],
            [
                'id_tarif' => 2,
                'jenis_kendaraan' => 'mobil',
                'tarif_perjam' => 5000,
                'created_at' => '2026-02-20 04:13:00',
                'updated_at' => '2026-02-20 04:13:00',
            ],
            [
                'id_tarif' => 3,
                'jenis_kendaraan' => 'lainnya',
                'tarif_perjam' => 8000,
                'created_at' => '2026-02-20 04:13:00',
                'updated_at' => '2026-02-20 04:13:00',
            ],
        ]);
    }
}

