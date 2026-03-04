<?php

namespace App\Http\Controllers;

use App\Models\ParkingMap;
use App\Models\ParkingMapCamera;
use App\Models\Camera;
use Illuminate\Http\Request;

class ParkingMapCameraController extends Controller
{
    public function index($parking_map_id)
    {
        $parkingMap = ParkingMap::with(['mapCameras.camera'])->findOrFail($parking_map_id);
        $cameras = Camera::viewer()->orderBy('nama')->get();
        $title = 'Kelola Kamera Peta: ' . $parkingMap->name;

        return view('parking_maps.cameras.index', compact('parkingMap', 'cameras', 'title'));
    }

    public function store(Request $request, $parking_map_id)
    {
        $parkingMap = ParkingMap::findOrFail($parking_map_id);

        $data = $request->validate([
            'camera_id' => 'required|exists:tb_kamera,id',
            'x' => 'required|integer|min:0',
            'y' => 'required|integer|min:0',
        ]);

        $exists = ParkingMapCamera::where('parking_map_id', $parkingMap->id)
            ->where('camera_id', $data['camera_id'])
            ->exists();

        if ($exists) {
            return redirect()
                ->route('parking-maps.cameras.index', $parkingMap)
                ->with('error', 'Kamera ini sudah ditambahkan ke peta ini.');
        }

        $data['parking_map_id'] = $parkingMap->id;
        ParkingMapCamera::create($data);

        return redirect()
            ->route('parking-maps.cameras.index', $parkingMap)
            ->with('success', 'Posisi kamera berhasil ditambahkan.');
    }

    public function destroy($parking_map_id, $id)
    {
        $parkingMap = ParkingMap::findOrFail($parking_map_id);
        $pmc = ParkingMapCamera::where('parking_map_id', $parkingMap->id)->findOrFail($id);
        $pmc->delete();

        return redirect()
            ->route('parking-maps.cameras.index', $parkingMap)
            ->with('success', 'Posisi kamera dihapus.');
    }
}
