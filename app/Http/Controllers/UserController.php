<?php

namespace App\Http\Controllers;

use App\Support\UserPhoto;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SaldoHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function topup(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($user, $request) {
            $amount = $request->amount;
            
            // Update both columns for consistency
            $user->increment('balance', $amount);
            $user->increment('saldo', $amount);

            SaldoHistory::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'topup',
                'description' => $request->description ?? 'Top up saldo manual oleh admin/petugas',
            ]);
        });

        return redirect()->back()->with('success', 'Saldo berhasil ditambahkan.');
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('rfid_uid', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $data['password'] = Hash::make($data['password']);
        $photoFile = $request->file('photo');
        unset($data['photo']);

        $user = User::create($data);

        if ($photoFile?->isValid()) {
            $user->update(UserPhoto::replaceWithUpload($photoFile, $user));
        }

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $photoFile = $request->file('photo');
        unset($data['photo']);

        if ($photoFile?->isValid()) {
            $data = array_merge($data, UserPhoto::replaceWithUpload($photoFile, $user));
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Gunakan forceDelete() untuk menghapus secara permanen dari database
        // karena model User menggunakan trait SoftDeletes
        $user->forceDelete();
        
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus secara permanen.');
    }
}
