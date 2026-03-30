<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\PlatNomorNormalizer;

class UserVehicleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kendaraans = Kendaraan::where('id_user', $user->id)
            ->orderBy('plat_nomor')
            ->get();

        return view('user.vehicles.index', compact('kendaraans', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'jenis_kendaraan' => 'required|string|max:20',
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:100',
        ]);

        $platNormalized = PlatNomorNormalizer::normalize($data['plat_nomor']);
        $exists = Kendaraan::where('id_user', $user->id)
            ->where('plat_nomor', $platNormalized)
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Plat nomor ini sudah ada di daftar kendaraan Anda.');
        }

        Kendaraan::create([
            'plat_nomor' => $platNormalized,
            'jenis_kendaraan' => $data['jenis_kendaraan'],
            'warna' => $data['warna'] ?? null,
            'pemilik' => $data['pemilik'] ?? $user->name,
            'id_user' => $user->id,
        ]);

        return redirect()->route('user.vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function update(Request $request, Kendaraan $vehicle)
    {
        $user = Auth::user();
        if ((int) $vehicle->id_user !== (int) $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'jenis_kendaraan' => 'required|string|max:20',
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:100',
        ]);

        $platNormalized = PlatNomorNormalizer::normalize($data['plat_nomor']);
        $exists = Kendaraan::where('id_user', $user->id)
            ->where('id_kendaraan', '!=', $vehicle->id_kendaraan)
            ->where('plat_nomor', $platNormalized)
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Plat nomor ini sudah ada di daftar kendaraan Anda.');
        }

        $vehicle->update([
            'plat_nomor' => $platNormalized,
            'jenis_kendaraan' => $data['jenis_kendaraan'],
            'warna' => $data['warna'] ?? null,
            'pemilik' => $data['pemilik'] ?? $user->name,
        ]);

        return redirect()->route('user.vehicles.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $vehicle)
    {
        $user = Auth::user();
        if ((int) $vehicle->id_user !== (int) $user->id) {
            abort(403);
        }

        $vehicle->delete();

        return redirect()->route('user.vehicles.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}

