<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use App\Models\Camera;
use App\Models\ParkingMapCamera;
use App\Models\ParkingMapSlot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AreaParkirSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key constraints to allow truncating
        Schema::disableForeignKeyConstraints();
        ParkingMapCamera::truncate();
        ParkingMapSlot::truncate();
        Schema::enableForeignKeyConstraints();

        $areas = [
            [
                'nama_area' => 'Lantai 1 Utama',
                'map_prefix' => 'LT1',
                'daerah' => 'Indoor',
                'kapasitas' => 50,
                'terisi' => 0,
                'is_default_map' => true,
                'map_code' => 'area-lt1',
                'map_width' => 1200,
                'map_height' => 800,
                'map_image' => 'https://res.cloudinary.com/demo/image/upload/v1631234567/sample.jpg', // Placeholder image
            ],
            [
                'nama_area' => 'Lantai 2 VIP',
                'map_prefix' => 'LT2',
                'daerah' => 'Indoor',
                'kapasitas' => 30,
                'terisi' => 0,
                'is_default_map' => false,
                'map_code' => 'area-lt2',
                'map_width' => 1000,
                'map_height' => 600,
                'map_image' => 'https://res.cloudinary.com/demo/image/upload/v1631234567/sample.jpg', // Placeholder image
            ],
            [
                'nama_area' => 'Area Terbuka Utara',
                'map_prefix' => 'ATU',
                'daerah' => 'Outdoor',
                'kapasitas' => 100,
                'terisi' => 0,
                'is_default_map' => false,
                'map_code' => 'area-atu',
                'map_width' => 1500,
                'map_height' => 1000,
                'map_image' => 'https://res.cloudinary.com/demo/image/upload/v1631234567/sample.jpg', // Placeholder image
            ],
        ];

        foreach ($areas as $areaData) {
            $area = AreaParkir::updateOrCreate(['map_prefix' => $areaData['map_prefix']], $areaData);

            // Create some slots for each area
            $this->createSlotsForArea($area);

            // Assign cameras to the area map
            $this->assignCamerasToArea($area);
        }
    }

    private function createSlotsForArea(AreaParkir $area)
    {
        $rows = 5;
        $cols = 10;
        $slotWidth = 60;
        $slotHeight = 40;
        $gapX = 80;
        $gapY = 60;
        $startX = 100;
        $startY = 100;

        $count = 0;
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if ($count >= $area->kapasitas) break;

                $code = $area->map_prefix . sprintf('%02d', $count + 1);
                ParkingMapSlot::create([
                    'area_parkir_id' => $area->id_area,
                    'code' => $code,
                    'x' => $startX + ($c * $gapX),
                    'y' => $startY + ($r * $gapY),
                    'width' => $slotWidth,
                    'height' => $slotHeight,
                ]);
                $count++;
            }
            if ($count >= $area->kapasitas) break;
        }
    }

    private function assignCamerasToArea(AreaParkir $area)
    {
        // Get viewer cameras matching the prefix
        $cameras = Camera::where('tipe', Camera::TIPE_VIEWER)
            ->where('nama', 'like', "%{$area->map_prefix}%")
            ->get();

        $x = 50;
        $y = 50;
        foreach ($cameras as $camera) {
            // Ensure this camera is not already assigned to another area map
            // (due to unique constraint in migration)
            if (!ParkingMapCamera::where('camera_id', $camera->id)->exists()) {
                ParkingMapCamera::create([
                    'area_parkir_id' => $area->id_area,
                    'camera_id' => $camera->id,
                    'x' => $x,
                    'y' => $y,
                ]);
                $y += 100; // Offset next camera if multiple
            }
        }
    }
}
