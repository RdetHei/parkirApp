<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingMap;

class ParkingSlotController extends Controller
{
    /**
     * API: kembalikan data slot parkir (dummy) untuk Leaflet.
     */
    public function index()
    {
        $slots = [
            [
                'id' => 1,
                'code' => 'A1',
                'x' => 300,
                'y' => 400,
                'width' => 80,
                'height' => 40,
                'status' => 'empty',
                'vehicle_plate' => null,
            ],
            [
                'id' => 2,
                'code' => 'A2',
                'x' => 420,
                'y' => 400,
                'width' => 80,
                'height' => 40,
                'status' => 'occupied',
                'vehicle_plate' => 'B 1234 XYZ',
            ],
            [
                'id' => 3,
                'code' => 'B1',
                'x' => 300,
                'y' => 460,
                'width' => 80,
                'height' => 40,
                'status' => 'reserved',
                'vehicle_plate' => 'D 9876 ABC',
            ],
        ];

        return response()->json($slots);
    }

    /**
     * Halaman peta parkir (Leaflet + image overlay).
     */
    public function view(Request $request)
    {
        $maps = ParkingMap::orderBy('name')->get();

        $selectedId = $request->query('map');
        $map = null;

        if ($selectedId) {
            $map = $maps->firstWhere('id', (int) $selectedId);
        }

        if (!$map) {
            $map = ParkingMap::getDefaultOrFirst();
        }

        return view('parking-map', [
            'map' => $map,
            'maps' => $maps,
        ]);
    }
}

