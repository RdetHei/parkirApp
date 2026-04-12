<?php

namespace App\Http\Controllers;

use App\Support\UserPhoto;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AreaParkir;
use App\Models\SaldoHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Kendaraan;
use App\Models\Transaksi;
use App\Models\Pembayaran;

class UserController extends Controller
{
    /**
     * User Dashboard
     */
    public function dashboard(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $totalKendaraan = Kendaraan::where('id_user', $user->id)->count();
        $totalTransaksi = Transaksi::where('id_user', $user->id)->count();
        $transaksiAktif = Transaksi::where('id_user', $user->id)
            ->where('status', 'masuk')
            ->count();

        $totalPembayaran = Pembayaran::where('id_user', $user->id)->count();
        $totalPengeluaran = Pembayaran::where('id_user', $user->id)
            ->where('status', 'berhasil')
            ->sum('nominal');

        // Tagihan (transaksi sudah keluar tapi belum dibayar)
        $transaksiBelumDibayar = Transaksi::where('id_user', $user->id)
            ->where('status', 'keluar')
            ->where(function ($q) {
                $q->whereNull('status_pembayaran')
                    ->orWhere('status_pembayaran', '!=', 'berhasil');
            })
            ->count();

        $riwayatTransaksi = Transaksi::with(['kendaraan', 'area', 'tarif'])
            ->where('id_user', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $riwayatPembayaran = Pembayaran::with(['transaksi'])
            ->where('id_user', $user->id)
            ->orderByDesc('waktu_pembayaran')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'user',
            'totalKendaraan',
            'totalTransaksi',
            'transaksiAktif',
            'totalPembayaran',
            'totalPengeluaran',
            'transaksiBelumDibayar',
            'riwayatTransaksi',
            'riwayatPembayaran'
        ));
    }

    /**
     * User: Riwayat transaksi lengkap
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $transactions = Transaksi::with(['kendaraan', 'area', 'tarif', 'pembayaran'])
            ->where('id_user', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);
        $title = 'Riwayat Parkir';
        return view('user.history', compact('transactions', 'title'));
    }

    /**
     * User: Profil sendiri
     */
    public function profile(Request $request)
    {
        return view('user.profile', ['user' => $request->user(), 'title' => 'Profil Saya']);
    }

    /**
     * User: Update profil sendiri
     */
    public function profileUpdate(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tb_user,email,' . $user->id,
            'phone' => 'nullable|string|max:32',
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->filled('password')) {
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
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function topup(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($user, $request) {
            $amount = $request->amount;

            $user->increment('balance', $amount);

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
        $areas = AreaParkir::all();
        return view('users.create', compact('title', 'areas'));
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
            'id_area' => 'nullable|exists:tb_area_parkir,id_area',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if (($data['role'] ?? '') === 'petugas' || ($data['role'] ?? '') === 'user') {
            $data['id_area'] = null;
        }

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
        $areas = AreaParkir::all();
        return view('users.edit', compact('user', 'title', 'areas'));
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
            'id_area' => 'nullable|exists:tb_area_parkir,id_area',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if (($data['role'] ?? '') === 'petugas') {
            $data['id_area'] = null;
        }
        if (($data['role'] ?? '') === 'user') {
            $data['id_area'] = null;
        }

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
