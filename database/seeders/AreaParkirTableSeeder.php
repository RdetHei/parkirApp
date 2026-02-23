<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use Illuminate\Database\Seeder;

class AreaParkirTableSeeder extends Seeder
{
    public function run(): void
    {
        AreaParkir::query()->delete();

        AreaParkir::insert([
            [
                'id_area' => 1,
                'nama_area' => 'AREA 51',
                'kapasitas' => 100,
                'terisi' => 5,
                'created_at' => '2026-02-20 04:12:58',
                'updated_at' => '2026-02-22 15:00:36',
            ],
            [
                'id_area' => 2,
                'nama_area' => 'LOT A',
                'kapasitas' => 50,
                'terisi' => 10,
                'created_at' => '2026-02-20 04:12:59',
                'updated_at' => '2026-02-20 04:12:59',
            ],
            [
                'id_area' => 3,
                'nama_area' => 'LOT B',
                'kapasitas' => 30,
                'terisi' => 2,
                'created_at' => '2026-02-20 04:12:59',
                'updated_at' => '2026-02-20 04:12:59',
            ],
        ]);
    }
}

