<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use Illuminate\Database\Seeder;

class AreaParkirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AreaParkir::insert([
            [
                'nama_area' => 'Neston Garut Center',
                'daerah' => 'Garut',
                'kapasitas' => 50,
                'terisi' => 0,
                'map_code' => 'GRT-01',
                'map_image' => 'https://res.cloudinary.com/demo/image/upload/v1/neston/maps/default.png',
                'map_width' => 1000,
                'map_height' => 800,
                'is_default_map' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_area' => 'Neston Bandung Mall',
                'daerah' => 'Bandung',
                'kapasitas' => 100,
                'terisi' => 0,
                'map_code' => 'BDG-01',
                'map_image' => 'https://res.cloudinary.com/demo/image/upload/v1/neston/maps/default.png',
                'map_width' => 1200,
                'map_height' => 900,
                'is_default_map' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_area' => 'Neston Jakarta Plaza',
                'daerah' => 'Jakarta',
                'kapasitas' => 200,
                'terisi' => 0,
                'map_code' => 'JKT-01',
                'map_image' => 'https://res.cloudinary.com/demo/image/upload/v1/neston/maps/default.png',
                'map_width' => 1500,
                'map_height' => 1000,
                'is_default_map' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
