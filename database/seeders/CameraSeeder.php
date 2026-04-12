<?php

namespace Database\Seeders;

use App\Models\Camera;
use Illuminate\Database\Seeder;

class CameraSeeder extends Seeder
{
    public function run(): void
    {
        $cameras = [
            [
                'nama' => 'Pintu Masuk Utama',
                'url' => 'http://camera1.local/stream',
                'tipe' => Camera::TIPE_SCANNER,
                'is_default' => true,
            ],
            [
                'nama' => 'Pintu Keluar Utama',
                'url' => 'http://camera2.local/stream',
                'tipe' => Camera::TIPE_SCANNER,
                'is_default' => false,
            ],
            [
                'nama' => 'Viewer LT1 - Area A',
                'url' => 'http://camera3.local/stream',
                'tipe' => Camera::TIPE_VIEWER,
                'is_default' => false,
            ],
            [
                'nama' => 'Viewer LT1 - Area B',
                'url' => 'http://camera4.local/stream',
                'tipe' => Camera::TIPE_VIEWER,
                'is_default' => false,
            ],
            [
                'nama' => 'Viewer LT2 - VIP',
                'url' => 'http://camera5.local/stream',
                'tipe' => Camera::TIPE_VIEWER,
                'is_default' => false,
            ],
            [
                'nama' => 'Viewer ATU - Utara',
                'url' => 'http://camera6.local/stream',
                'tipe' => Camera::TIPE_VIEWER,
                'is_default' => false,
            ],
        ];

        foreach ($cameras as $camera) {
            Camera::updateOrCreate(['nama' => $camera['nama']], $camera);
        }
    }
}
