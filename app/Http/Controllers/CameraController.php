<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camera;

class CameraController extends Controller
{
    public function index(Request $request)
    {
        $query = Camera::query();

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
