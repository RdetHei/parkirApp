<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RfidAdminController extends Controller
{
    public function index()
    {
        $title = 'Manajemen Kartu RFID';
        $users = User::orderBy('id', 'desc')->paginate(15);
        return view('rfid.index', compact('title', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:tb_user,id'],
            'rfid_uid' => [
                'required',
                'string',
                'max:128',
                Rule::unique('tb_user', 'rfid_uid')->ignore((int) $request->input('user_id')),
            ],
        ]);

        $user = User::findOrFail((int) $data['user_id']);
        $user->rfid_uid = $data['rfid_uid'];
        $user->nfc_uid = $data['rfid_uid']; // Sync for compatibility
        $user->save();

        return back()->with('success', 'RFID berhasil didaftarkan untuk ' . $user->name);
    }

    public function unlink($id)
    {
        $user = User::findOrFail($id);
        $user->rfid_uid = null;
        $user->nfc_uid = null;
        $user->save();

        return back()->with('success', 'RFID berhasil dihapus dari akun ' . $user->name);
    }
}

