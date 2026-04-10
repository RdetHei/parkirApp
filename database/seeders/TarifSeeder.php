<?php

namespace Database\Seeders;

use App\Models\Tarif;
use Illuminate\Database\Seeder;

class TarifSeeder extends Seeder
{
    public function run(): void
    {
        $tarifs = [
            [
                'jenis_kendaraan' => 'motor',
                'tarif_perjam' => 2000,
            ],
            [
                'jenis_kendaraan' => 'mobil',
                'tarif_perjam' => 5000,
            ],
        ];

        foreach ($tarifs as $tarif) {
            Tarif::updateOrCreate(['jenis_kendaraan' => $tarif['jenis_kendaraan']], $tarif);
        }
    }
}
