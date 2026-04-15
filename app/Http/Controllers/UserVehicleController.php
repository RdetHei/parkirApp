<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Tarif;
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

        $vehicleTypes = Tarif::pluck('jenis_kendaraan')->toArray();

        return view('user.vehicles.index', compact('kendaraans', 'user', 'vehicleTypes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Batasi maksimal 2 kendaraan per user
        $count = Kendaraan::where('id_user', $user->id)->count();
        if ($count >= 2) {
            return back()->with('error', 'Anda hanya dapat mendaftarkan maksimal 2 kendaraan.');
        }

        $vehicleTypes = Tarif::pluck('jenis_kendaraan')->toArray();
        $typeIn = implode(',', $vehicleTypes);

        $data = $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'jenis_kendaraan' => "required|string|in:{$typeIn}",
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

        // Cek apakah kendaraan sedang parkir (sesuaikan dengan tabel parking_logs)
        $isParking = \App\Models\ParkingLog::where('id_kendaraan', $vehicle->id_kendaraan)
            ->whereNull('checkout_time')
            ->exists();

        if ($isParking) {
            return back()->with('error', 'Kendaraan sedang parkir. Data tidak dapat diubah.');
        }

        $vehicleTypes = Tarif::pluck('jenis_kendaraan')->toArray();
        $typeIn = implode(',', $vehicleTypes);

        $data = $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'jenis_kendaraan' => "required|string|in:{$typeIn}",
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:100',
        ]);

        $platNormalized = PlatNomorNormalizer::normalize($data['plat_nomor']);

        // Jika plat nomor berubah, cek apakah sudah ada RFID terhubung
        if ($platNormalized !== $vehicle->plat_nomor) {
            $hasRfid = \App\Models\RfidTag::where('id_kendaraan', $vehicle->id_kendaraan)->exists();
            if ($hasRfid) {
                return back()->with('error', 'Plat nomor tidak dapat diubah karena sudah terhubung dengan kartu RFID. Silakan hubungi admin.');
            }
        }

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

        // Cek apakah kendaraan sedang parkir
        $isParking = \App\Models\ParkingLog::where('id_kendaraan', $vehicle->id_kendaraan)
            ->whereNull('checkout_time')
            ->exists();

        if ($isParking) {
            return back()->with('error', 'Kendaraan sedang parkir. Data tidak dapat dihapus.');
        }

        // Cek apakah ada RFID terhubung
        $hasRfid = \App\Models\RfidTag::where('id_kendaraan', $vehicle->id_kendaraan)->exists();
        if ($hasRfid) {
            return back()->with('error', 'Kendaraan tidak dapat dihapus karena sudah terhubung dengan kartu RFID. Silakan hubungi admin.');
        }

        $vehicle->delete();

        return redirect()->route('user.vehicles.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}

