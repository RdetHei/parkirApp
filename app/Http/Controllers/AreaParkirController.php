<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AreaParkir;
use App\Models\ParkingMap;

class AreaParkirController extends Controller
{
    public function index()
    {
        $areas = AreaParkir::with('parkingMap')->orderBy('id_area', 'desc')->paginate(15);
        $title = 'Data Area Parkir';
        return view('area_parkir.index', compact('areas', 'title'));
    }

    public function create()
    {
        $title = 'Tambah Area Parkir';
        return view('area_parkir.create', compact('title'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_area' => 'required|string|max:50|unique:tb_area_parkir,nama_area',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $area = AreaParkir::create($data);

        // Trigger: buat layout peta otomatis (1:1 dengan area)
        ParkingMap::create([
            'area_parkir_id' => $area->id_area,
            'name' => $area->nama_area,
            'code' => 'area-' . $area->id_area,
            'image_path' => 'images/floor1.png',
            'width' => 1000,
            'height' => 800,
            'is_default' => false,
        ]);

        return redirect()->route('area-parkir.index')->with('success', 'Area parkir dan layout peta berhasil dibuat.');
    }

    public function edit($id)
    {
        $area = AreaParkir::findOrFail($id);
        $title = 'Edit Area Parkir';
        return view('area_parkir.edit', compact('area', 'title'));
    }

    public function update(Request $request, $id)
    {
        $area = AreaParkir::findOrFail($id);
        $data = $request->validate([
            'nama_area' => 'required|string|max:50|unique:tb_area_parkir,nama_area,' . $id . ',id_area',
            'kapasitas' => 'required|integer|min:1',
        ]);
        $area->update($data);

        // Sinkron: nama layout peta ikut nama area
        if ($area->parkingMap) {
            $area->parkingMap->update(['name' => $area->nama_area]);
        }

        return redirect()->route('area-parkir.index')->with('success', 'Area parkir dan layout peta berhasil diubah.');
    }

    public function destroy($id)
    {
        $area = AreaParkir::findOrFail($id);

        if ($area->transaksis()->exists()) {
            return redirect()->route('area-parkir.index')->with('error', 'Area parkir tidak dapat dihapus karena masih digunakan dalam transaksi.');
        }

        // Hapus area; layout peta terkait ikut terhapus (cascade)
        $area->delete();

        return redirect()->route('area-parkir.index')->with('success', 'Area parkir dan layout peta berhasil dihapus.');
    }

    /** Buat layout peta untuk area yang sudah ada (belum punya layout). */
    public function createLayout(AreaParkir $area)
    {
        if ($area->parkingMap) {
            return redirect()->route('parking-maps.edit', $area->parkingMap)->with('info', 'Area ini sudah punya layout peta.');
        }
        $map = ParkingMap::create([
            'area_parkir_id' => $area->id_area,
            'name' => $area->nama_area,
            'code' => 'area-' . $area->id_area,
            'image_path' => 'images/floor1.png',
            'width' => 1000,
            'height' => 800,
            'is_default' => false,
        ]);
        return redirect()->route('parking-maps.edit', $map)->with('success', 'Layout peta untuk area ini telah dibuat.');
    }
}

