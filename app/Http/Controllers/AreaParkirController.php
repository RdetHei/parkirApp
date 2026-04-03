<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Camera;
use App\Models\ParkingMapCamera;
use App\Models\ParkingMapSlot;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AreaParkirController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $areas = AreaParkir::withCount('slots')->orderBy('id_area', 'desc')->paginate(15);
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
            'daerah' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'map_code' => 'nullable|string|max:50',
            'map_image' => 'nullable|image|max:2048',
            'map_width' => 'nullable|integer|min:100',
            'map_height' => 'nullable|integer|min:100',
            'is_default_map' => 'nullable',
        ]);

        $data['is_default_map'] = $request->has('is_default_map');

        if ($data['is_default_map']) {
            AreaParkir::where('is_default_map', true)->update(['is_default_map' => false]);
        }

        if ($request->hasFile('map_image')) {
            $file = $request->file('map_image');
            $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'neston/maps'
            ]);
            $data['map_image'] = $upload['secure_url'];
        } else {
            $data['map_image'] = 'https://res.cloudinary.com/' . env('CLOUDINARY_CLOUD_NAME') . '/image/upload/v1/neston/maps/default.png';
        }

        if (empty($data['map_code'])) {
            $data['map_code'] = 'area-' . time();
        }

        $area = AreaParkir::create($data);

        $this->logActivity(
            "Menambahkan area parkir baru: {$area->nama_area}",
            'config',
            $area,
            $data
        );

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
            'daerah' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'map_code' => 'nullable|string|max:50',
            'map_image' => 'nullable|image|max:2048',
            'map_width' => 'nullable|integer|min:100',
            'map_height' => 'nullable|integer|min:100',
            'is_default_map' => 'nullable',
        ]);

        $data['is_default_map'] = $request->has('is_default_map');

        if ($data['is_default_map']) {
            AreaParkir::where('id_area', '!=', $id)->where('is_default_map', true)->update(['is_default_map' => false]);
        }

        $oldData = $area->toArray();

        if ($request->hasFile('map_image')) {
            // Delete old local image if exists
            if ($area->map_image && !str_starts_with($area->map_image, 'http')) {
                Storage::disk('public')->delete($area->map_image);
            }

            $file = $request->file('map_image');
            $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'neston/maps'
            ]);
            $data['map_image'] = $upload['secure_url'];
        }

        $area->update($data);

        $this->logActivity(
            "Mengubah area parkir: {$area->nama_area}",
            'config',
            $area,
            ['old' => $oldData, 'new' => $data]
        );

        return redirect()->route('area-parkir.index')->with('success', 'Area parkir dan layout peta berhasil diubah.');
    }

    public function destroy($id)
    {
        $area = AreaParkir::findOrFail($id);

        if ($area->transaksis()->exists()) {
            return redirect()->route('area-parkir.index')->with('error', 'Area parkir tidak dapat dihapus karena masih digunakan dalam transaksi.');
        }

        if ($area->map_image && !str_starts_with($area->map_image, 'http')) {
            Storage::disk('public')->delete($area->map_image);
        }

        $this->logActivity(
            "Menghapus area parkir: {$area->nama_area}",
            'config',
            null,
            $area->toArray()
        );

        $area->delete();

        return redirect()->route('area-parkir.index')->with('success', 'Area parkir berhasil dihapus.');
    }

    /**
     * Editor Visual untuk Peta Area
     */
    public function design($id)
    {
        $area = AreaParkir::findOrFail($id);
        $cameras = Camera::all();
        $title = "Desain Peta: " . $area->nama_area;

        return view('area_parkir.design', compact('area', 'cameras', 'title'));
    }

    /**
     * Simpan Layout Peta (Slots & Cameras)
     */
    public function saveDesign(Request $request, $id)
    {
        $area = AreaParkir::findOrFail($id);
        $data = $request->validate([
            'slots' => 'nullable|array',
            'cameras' => 'nullable|array',
        ]);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($area, $data) {
                // Sync Slots
                ParkingMapSlot::where('area_parkir_id', $area->id_area)->delete();
                if (!empty($data['slots'])) {
                    foreach ($data['slots'] as $slot) {
                        ParkingMapSlot::create([
                            'area_parkir_id' => $area->id_area,
                            'code' => $slot['code'],
                            'x' => $slot['x'],
                            'y' => $slot['y'],
                            'width' => $slot['width'] ?? 60,
                            'height' => $slot['height'] ?? 40,
                            'camera_id' => (!empty($slot['camera_id'])) ? $slot['camera_id'] : null,
                            'notes' => $slot['notes'] ?? null,
                        ]);
                    }
                }

                // Sync Cameras
                ParkingMapCamera::where('area_parkir_id', $area->id_area)->delete();
                if (!empty($data['cameras'])) {
                    foreach ($data['cameras'] as $cam) {
                        ParkingMapCamera::create([
                            'area_parkir_id' => $area->id_area,
                            'camera_id' => $cam['camera_id'],
                            'x' => $cam['x'],
                            'y' => $cam['y'],
                        ]);
                    }
                }

                return response()->json(['success' => true, 'message' => 'Desain peta berhasil disimpan.']);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving design: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }
}
