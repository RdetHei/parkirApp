<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RfidIdentifyController extends Controller
{
    public function page()
    {
        $title = 'RFID Identifikasi';
        return view('rfid.identify', compact('title'));
    }

    public function identify(Request $request)
    {
        $data = $request->validate([
            'rfid_uid' => ['required', 'string', 'max:128', 'regex:/^[0-9A-Za-z]+$/'],
        ]);

        $user = User::query()->where('rfid_uid', $data['rfid_uid'])->first();
        if (! $user) {
            return response()->json([
                'ok' => false,
                'error' => 'UID RFID tidak terdaftar.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'user' => [
                'user_id' => (int) $user->id,
                'name' => $user->name,
                'photo' => $user->photo,
                'saldo' => (float) ($user->saldo ?? 0),
                'balance' => (float) ($user->balance ?? $user->saldo ?? 0),
                'role' => $user->role,
            ],
        ]);
    }
}

