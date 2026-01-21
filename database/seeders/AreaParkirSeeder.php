<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AreaParkir;

class AreaParkirSeeder extends Seeder
{
    public function run(): void
    {
        AreaParkir::create(["nama_area" => 'AREA 51', "kapasitas" => 100, "terisi" => 5]);
        AreaParkir::create(["nama_area" => 'LOT A', "kapasitas" => 50, "terisi" => 10]);
        AreaParkir::create(["nama_area" => 'LOT B', "kapasitas" => 30, "terisi" => 2]);
    }
}
