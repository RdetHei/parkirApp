<?php

namespace App\Http\Controllers;

use App\Models\ParkingMap;
use App\Models\Camera;
use Illuminate\Http\Request;

class ParkingMapController extends Controller
{
    public function index()
    {
        $items = ParkingMap::with('areaParkir')->orderByDesc('is_default')->orderBy('name')->paginate(15);
        return view('parking_maps.index', compact('items'));
    }

    public function create()
    {
        return view('parking_maps.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:tb_parking_maps,code',
            'image_path' => 'required|string|max:255',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'is_default' => 'nullable|boolean',
        ]);

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            ParkingMap::query()->update(['is_default' => false]);
        }

        ParkingMap::create($data);

        return redirect()->route('parking-maps.index')->with('success', 'Peta parkir berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = ParkingMap::findOrFail($id);
        $item->load('mapCameras.camera');
        $cameras = Camera::orderBy('nama')->get();
        return view('parking_maps.edit', compact('item', 'cameras'));
    }

    public function update(Request $request, $id)
    {
        $item = ParkingMap::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:tb_parking_maps,code,' . $item->id,
            'image_path' => 'required|string|max:255',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'is_default' => 'nullable|boolean',
        ]);

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            ParkingMap::where('id', '!=', $item->id)->update(['is_default' => false]);
        }

        $item->update($data);

        return redirect()->route('parking-maps.index')->with('success', 'Peta parkir berhasil diubah.');
    }

    public function destroy($id)
    {
        $item = ParkingMap::findOrFail($id);
        $item->delete();

        return redirect()->route('parking-maps.index')->with('success', 'Peta parkir berhasil dihapus.');
    }
}

