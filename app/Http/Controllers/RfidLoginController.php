<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RfidLoginController extends Controller
{
    public function page()
    {
        $title = 'Login RFID';
        return view('auth.rfid-login', compact('title'));
    }

    public function login(Request $request)
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
            $user = User::query()->where('rfid_uid', $uid)->first();
        }

        if (! $user) {
            return response()->json([
                'ok' => false,
                'error' => 'Kartu tidak terdaftar.',
            ], 404);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $role = strtolower($user->role ?? 'user');
        $redirect = match ($role) {
            'admin' => route('dashboard'),
            'owner' => route('owner.dashboard'),
            'petugas' => route('petugas.dashboard'),
            default => route('user.dashboard'),
        };

        return response()->json([
            'ok' => true,
            'message' => 'Login berhasil.',
            'redirect' => $redirect,
            'user' => [
                'user_id' => (int) $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
        ]);
    }
}

