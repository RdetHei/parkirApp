<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RfidAccessController extends Controller
{
    public function scanPage()
    {
        $title = 'RFID Terminal';
        return view('parkir.scan', compact('title'));
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'rfid_uid' => ['required', 'string', 'max:128', 'regex:/^[0-9A-Za-z]+$/'],
        ]);

        $uid = $data['rfid_uid'];
        $user = null;

        // Coba cari di RfidTag dulu
        $rfidTag = \App\Models\RfidTag::with('kendaraan.user')->where('uid', $uid)->first();

        if ($rfidTag && $rfidTag->kendaraan) {
            $user = $rfidTag->kendaraan->user;
        } else {
            // Fallback ke tb_user.rfid_uid
            $user = User::query()
                ->where('rfid_uid', $uid)
                ->first();
        }

        if (! $user) {
            return response()->json([
                'ok' => false,
                'error' => 'UID RFID tidak terdaftar.',
            ], 404);
        }

        // Set session "akses via kartu" (bukan login).
        $request->session()->put('rfid_access_user_id', (int) $user->id);
        $request->session()->put('rfid_access_role', (string) ($user->role ?? ''));

        return response()->json([
            'ok' => true,
            'message' => 'Akses kartu terverifikasi.',
            'user' => [
                'user_id' => (int) $user->id,
                'name' => $user->name,
                'photo' => $user->profile_photo_url,
                'saldo' => (float) ($user->balance ?? $user->saldo ?? 0),
                'role' => $user->role,
            ],
        ]);
    }

    public function clear(Request $request)
    {
        $request->session()->forget(['rfid_access_user_id', 'rfid_access_role']);
        return redirect()->back()->with('success', 'RFID access cleared.');
    }
}

