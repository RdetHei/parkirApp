<?php

namespace App\Http\Controllers;

use App\Models\ParkingMap;
use App\Models\ParkingMapSlot;
use App\Models\AreaParkir;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParkingMapSlotController extends Controller
{
    public function index($parking_map_id)
    {
        $parkingMap = ParkingMap::findOrFail($parking_map_id);
        $slots = ParkingMapSlot::where('parking_map_id', $parkingMap->id)
            ->with(['areaParkir', 'camera'])
            ->orderBy('code')
            ->paginate(20);

        return view('parking_maps.slots.index', compact('parkingMap', 'slots'));
    }

    public function create($parking_map_id)
    {
        $parkingMap = ParkingMap::findOrFail($parking_map_id);
        $areas = AreaParkir::orderBy('nama_area')->get();
        $cameras = Camera::orderBy('nama')->get();

        return view('parking_maps.slots.create', compact('parkingMap', 'areas', 'cameras'));
    }

    public function store(Request $request, $parking_map_id)
    {
        $parkingMap = ParkingMap::findOrFail($parking_map_id);

        $data = $request->validate([
            'code' => 'required|string|max:30',
            'x' => 'required|integer|min:0',
            'y' => 'required|integer|min:0',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'area_parkir_id' => 'nullable|exists:tb_area_parkir,id_area',
            'camera_id' => 'nullable|exists:tb_kamera,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $data['parking_map_id'] = $parkingMap->id;

        $request->validate([
            'code' => [Rule::unique('tb_parking_map_slots', 'code')->where('parking_map_id', $parkingMap->id)],
        ], [
            'code.unique' => 'Kode slot ini sudah dipakai di peta ini.',
        ]);

        ParkingMapSlot::create($data);

        return redirect()
            ->route('parking-maps.slots.index', $parkingMap)
            ->with('success', 'Slot berhasil ditambahkan.');
    }

    public function edit($parking_map_id, $id)
    {
        $parkingMap = ParkingMap::findOrFail($parking_map_id);
        $slot = ParkingMapSlot::where('parking_map_id', $parkingMap->id)->findOrFail($id);
        $areas = AreaParkir::orderBy('nama_area')->get();
        $cameras = Camera::orderBy('nama')->get();

        return view('parking_maps.slots.edit', compact('parkingMap', 'slot', 'areas', 'cameras'));
    }

    public function update(Request $request, $parking_map_id, $id)
    {
        $parkingMap = ParkingMap::findOrFail($parking_map_id);
        $slot = ParkingMapSlot::where('parking_map_id', $parkingMap->id)->findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|max:30',
            'x' => 'required|integer|min:0',
            'y' => 'required|integer|min:0',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'area_parkir_id' => 'nullable|exists:tb_area_parkir,id_area',
            'camera_id' => 'nullable|exists:tb_kamera,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $request->validate([
            'code' => [Rule::unique('tb_parking_map_slots', 'code')->where('parking_map_id', $parkingMap->id)->ignore($slot->id)],
        ], [
            'code.unique' => 'Kode slot ini sudah dipakai di peta ini.',
        ]);

        $slot->update($data);

        return redirect()
            ->route('parking-maps.slots.index', $parkingMap)
            ->with('success', 'Slot berhasil diubah.');
    }

    public function destroy($parking_map_id, $id)
    {
        $parkingMap = ParkingMap::findOrFail($parking_map_id);
        $slot = ParkingMapSlot::where('parking_map_id', $parkingMap->id)->findOrFail($id);
        $slot->delete();

        return redirect()
            ->route('parking-maps.slots.index', $parkingMap)
            ->with('success', 'Slot berhasil dihapus.');
    }
}
