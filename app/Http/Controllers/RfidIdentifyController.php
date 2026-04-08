<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kendaraan;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class RfidIdentifyController extends Controller
{
    public function page()
    {
        $title = 'RFID Terminal';
        return view('parkir.scan', compact('title'));
    }

    public function identify(Request $request)
    {
        $data = $request->validate([
            'rfid_uid' => ['required', 'string', 'max:128', 'regex:/^[0-9A-Za-z]+$/'],
        ]);

        $user = User::query()
            ->where('rfid_uid', $data['rfid_uid'])
            ->orWhere('nfc_uid', $data['rfid_uid'])
            ->first();

        if (! $user) {
            return response()->json([
                'ok' => false,
                'error' => 'UID RFID tidak terdaftar.',
            ], 404);
        }

        // Ambil kendaraan user
        $kendaraans = Kendaraan::where('id_user', $user->id)->get()->map(function($k) {
            return [
                'plat_nomor' => $k->plat_nomor,
                'jenis' => $k->jenis_kendaraan,
                'warna' => $k->warna,
            ];
        });

        // Ambil status parkir aktif (jika ada)
        $activeParking = Transaksi::with(['area', 'kendaraan'])
            ->where('id_user', $user->id)
            ->where('status', 'masuk')
            ->latest('waktu_masuk')
            ->first();

        return response()->json([
            'ok' => true,
            'user' => [
                'user_id' => (int) $user->id,
                'name' => $user->name,
                'photo' => $user->profile_photo_url,
                'saldo' => (float) ($user->saldo ?? 0),
                'balance' => (float) ($user->balance ?? $user->saldo ?? 0),
                'role' => $user->role,
                'kendaraans' => $kendaraans,
                'active_parking' => $activeParking ? [
                    'plat_nomor' => $activeParking->kendaraan->plat_nomor ?? '-',
                    'area' => $activeParking->area->nama_area ?? '-',
                    'waktu_masuk' => $activeParking->waktu_masuk->format('d M Y H:i'),
                ] : null,
            ],
        ]);
    }
}

