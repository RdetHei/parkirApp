<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarif;

class TarifSeeder extends Seeder
{
    public function run(): void
    {
        Tarif::create(['jenis_kendaraan' => 'motor', 'tarif_perjam' => 2000]);
        Tarif::create(['jenis_kendaraan' => 'mobil', 'tarif_perjam' => 5000]);
        Tarif::create(['jenis_kendaraan' => 'lainnya', 'tarif_perjam' => 8000]);
    }
}
