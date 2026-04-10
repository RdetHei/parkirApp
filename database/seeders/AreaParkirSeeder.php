<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use Illuminate\Database\Seeder;

class AreaParkirSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [
                'nama_area' => 'Lantai 1 Utama',
                'map_prefix' => 'LT1',
                'daerah' => 'Indoor',
                'kapasitas' => 50,
                'terisi' => 0,
                'is_default_map' => true,
                'map_code' => 'area-lt1',
            ],
            [
                'nama_area' => 'Lantai 2 VIP',
                'map_prefix' => 'LT2',
                'daerah' => 'Indoor',
                'kapasitas' => 30,
                'terisi' => 0,
                'is_default_map' => false,
                'map_code' => 'area-lt2',
            ],
            [
                'nama_area' => 'Area Terbuka Utara',
                'map_prefix' => 'ATU',
                'daerah' => 'Outdoor',
                'kapasitas' => 100,
                'terisi' => 0,
                'is_default_map' => false,
                'map_code' => 'area-atu',
            ],
        ];

        foreach ($areas as $area) {
            AreaParkir::updateOrCreate(['map_prefix' => $area['map_prefix']], $area);
        }
    }
}
