<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(15);
        $title = 'Data Pengguna';
        return view('users.index', compact('users', 'title'));
    }

    public function create()
    {
        $title = 'Tambah User';
        return view('users.create', compact('title'));
    }

    public function store(Request $request)
    {
        return $this->storeUser($request);
    }

    // Simpan user
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('tb_user')],
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:user,admin,petugas',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $data['photo'] = $path;
        }

        $user = User::create($data);

        return redirect()
            ->route('users.scan-rfid', $user->id)
            ->with('success', 'User berhasil dibuat. Silakan scan kartu RFID.');
    }

    public function showScanPage($id)
    {
        $user = User::findOrFail($id);
        $title = 'Scan RFID';
        return view('users.scan-rfid', compact('user', 'title'));
    }

    public function saveRfid(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'rfid_uid' => ['required', 'string', 'max:128', Rule::unique('tb_user', 'rfid_uid')->ignore($user->id)],
        ]);

        $user->rfid_uid = $request->rfid_uid;
        // Sync ke nfc_uid agar kompatibel dengan fitur lama jika ada
        $user->nfc_uid = $request->rfid_uid;
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'RFID berhasil didaftarkan.',
                'redirect' => route('users.show', $user->id)
            ]);
        }

        return redirect()
            ->route('users.show', $user->id)
            ->with('success', 'RFID berhasil didaftarkan.');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $title = 'Detail User';
        return view('users.show', compact('user', 'title'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $title = 'Edit User';
        return view('users.edit', compact('user', 'title'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tb_user,email,' . $user->id . ',id',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:user,admin,petugas',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
