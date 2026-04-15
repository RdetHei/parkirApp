<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camera;
use App\Models\AreaParkir;
use App\Models\ParkingMapCamera;
use Illuminate\Support\Facades\Auth;

class CameraController extends Controller
{
    public function monitor()
    {
        $user = Auth::user();
        $area = null;

        if ($user && $user->role === 'petugas') {
            $area = $this->resolveOperationalArea($user);
        }

        if ($user && $user->role === 'petugas') {
            if (! $area) {
                return view('kamera.monitor', [
                    'title' => 'Camera Monitoring',
                    'cameras' => collect(),
                    'activeArea' => null,
                    'needsOperationalArea' => true,
                ]);
            }

            $cameraIds = ParkingMapCamera::query()
                ->where('area_parkir_id', $area->id_area)
                ->pluck('camera_id')
                ->all();

            $cameras = Camera::query()
                ->whereIn('id', $cameraIds)
                ->orderByDesc('is_default')
                ->orderBy('tipe')
                ->orderBy('nama')
                ->get();
        } else {
            $cameras = Camera::query()
                ->orderByDesc('is_default')
                ->orderBy('tipe')
                ->orderBy('nama')
                ->get();
        }

        return view('kamera.monitor', [
            'title' => 'Camera Monitoring',
            'cameras' => $cameras,
            'activeArea' => $area,
            'needsOperationalArea' => false,
        ]);
    }

    private function resolveOperationalArea($user): ?AreaParkir
    {
        if ($user->id_area) {
            $area = AreaParkir::find($user->id_area);
            if ($area) {
                return $area;
            }
        }

        $sessionId = session(PetugasDashboardController::SESSION_OPERATIONAL_AREA);
        if ($sessionId) {
            return AreaParkir::find($sessionId);
        }

        return null;
    }

    public function index(Request $request)
    {
        $query = Camera::query()->with(['mapAssignment.areaParkir']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('nama', 'like', "%{$search}%");
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $items = $query->orderBy('is_default', 'desc')
                      ->orderBy('id', 'desc')
                      ->paginate(15)
                      ->withQueryString();

        return view('kamera.index', compact('items'));
    }

    public function create()
    {
        return view('kamera.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'url' => 'required|string|url|max:500',
            'tipe' => 'required|in:'.Camera::TIPE_SCANNER.','.Camera::TIPE_VIEWER,
            'is_default' => 'nullable|boolean',
        ]);

        // Auto-append /video if user only provides IP and Port for IP Webcam
        $url = $data['url'];
        if ($data['tipe'] === Camera::TIPE_SCANNER && strpos($url, '/', 8) === false) {
            $data['url'] = rtrim($url, '/') . '/video';
        }

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Camera::query()->update(['is_default' => false]);
        }

        Camera::create($data);

        return redirect()->route('kamera.index')->with('success', 'Kamera berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = Camera::findOrFail($id);
        return view('kamera.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Camera::findOrFail($id);
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'url' => 'required|string|url|max:500',
            'tipe' => 'required|in:'.Camera::TIPE_SCANNER.','.Camera::TIPE_VIEWER,
            'is_default' => 'nullable|boolean',
        ]);

        // Auto-append /video if user only provides IP and Port for IP Webcam
        $url = $data['url'];
        if ($data['tipe'] === Camera::TIPE_SCANNER && strpos($url, '/', 8) === false) {
            $data['url'] = rtrim($url, '/') . '/video';
        }

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Camera::where('id', '!=', $id)->update(['is_default' => false]);
        }

        $item->update($data);

        return redirect()->route('kamera.index')->with('success', 'Kamera berhasil diubah.');
    }

    public function destroy($id)
    {
        $item = Camera::findOrFail($id);
        $item->delete();

        return redirect()->route('kamera.index')->with('success', 'Kamera berhasil dihapus.');
    }
}
