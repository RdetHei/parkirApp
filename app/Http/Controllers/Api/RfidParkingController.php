<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RfidTag;
use App\Models\ParkingLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RfidParkingController extends Controller
{
    /**
     * Handle RFID Check-in
     *
     * POST /api/rfid/checkin
     * {
     *   "uid": "RFID123456"
     * }
     */
    public function checkin(Request $request)
    {
        // Cari RFID berdasarkan uid
        $rfid = RfidTag::with('kendaraan')->where('uid', $request->uid)->first();

        // Jika UID tidak ditemukan -> return error JSON 404.
        if (!$rfid) {
            return response()->json([
                'success' => false,
                'message' => 'RFID tidak ditemukan.'
            ], 404);
        }

        // Pastikan status = active
        // Jika RFID inactive -> return error JSON 403.
        if ($rfid->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'RFID inactive. Akses ditolak.'
            ], 403);
        }

        $vehicle = $rfid->kendaraan;
        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'RFID tidak terhubung ke kendaraan.'
            ], 404);
        }

        // Validasi: Tidak boleh check-in jika belum check-out sebelumnya
        $latestLog = ParkingLog::where('id_kendaraan', $vehicle->id_kendaraan)
            ->whereNull('checkout_time')
            ->orderBy('checkin_time', 'desc')
            ->first();

        if ($latestLog) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan sudah berada di dalam area parkir (Belum check-out).',
                'plate_number' => $vehicle->plat_nomor,
                'checkin_time' => $latestLog->checkin_time->toDateTimeString(),
            ], 400);
        }

        try {
            return DB::transaction(function () use ($vehicle) {
                // Simpan ke parking_logs sebagai check-in
                $parkingLog = ParkingLog::create([
                    'id_kendaraan' => $vehicle->id_kendaraan,
                    'checkin_time' => now(),
                    'gate_type' => 'masuk',
                    'tariff_amount' => null, // Sesuai requirement, tariff_amount nullable
                ]);

                // Return response:
                // * plate_number
                // * vehicle_type
                // * checkin_time
                return response()->json([
                    'plate_number' => $vehicle->plat_nomor,
                    'vehicle_type' => $vehicle->jenis_kendaraan,
                    'checkin_time' => $parkingLog->checkin_time->toDateTimeString(),
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle RFID Login
     *
     * POST /api/rfid/login
     * {
     *   "uid": "RFID123456"
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
        ]);

        // Cari RFID berdasarkan uid, lalu ambil kendaraan dan pemiliknya (user)
        $rfid = RfidTag::with(['kendaraan.user'])->where('uid', $request->uid)->first();

        if (!$rfid) {
            return response()->json([
                'success' => false,
                'message' => 'RFID tidak terdaftar.'
            ], 404);
        }

        if ($rfid->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID dalam status tidak aktif.'
            ], 403);
        }

        $user = $rfid->kendaraan->user ?? null;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan untuk RFID ini.'
            ], 404);
        }

        // Login user secara otomatis (jika menggunakan session)
        // Atau return data user (jika untuk login terminal/API)
        auth()->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil via RFID.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'plate_number' => $rfid->kendaraan->plat_nomor
            ]
        ], 200);
    }
}
