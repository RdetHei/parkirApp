<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogAktifitas;
use App\Models\User;

class LogAktifitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktifitas::with('user');

        if ($request->filled('id_user')) {
            $query->where('id_user', $request->id_user);
        }

        if ($request->filled('tipe_aktivitas')) {
            $query->where('tipe_aktivitas', $request->tipe_aktivitas);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_aktivitas', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_aktivitas', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('q')) {
            $query->where('aktivitas', 'like', '%' . $request->q . '%');
        }

        $items = $query->orderBy('waktu_aktivitas', 'desc')->paginate(15)->withQueryString();
        // Limit users to top 50 for selection to keep it light
        $users = User::orderBy('name')->limit(50)->get();
        $types = LogAktifitas::whereNotNull('tipe_aktivitas')->distinct()->pluck('tipe_aktivitas');
        
        $title = 'Data Log Aktivitas';
        return view('log_aktivitas.index', compact('items', 'title', 'users', 'types'));
    }

    public function create()
    {
        $users = User::orderBy('name')->limit(50)->get();
        $title = 'Tambah Log Aktivitas';
        return view('log_aktivitas.create', compact('users', 'title'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_user' => 'required|exists:tb_user,id',
            'aktivitas' => 'required|string|max:255',
            'waktu_aktivitas' => 'required|date_format:Y-m-d H:i:s',
        ]);

        LogAktifitas::create($data);
        return redirect()->route('log-aktivitas.index')->with('success','Log aktivitas berhasil dibuat');
    }

    public function show($id)
    {
        $item = LogAktifitas::with('user')->findOrFail($id);
        $title = 'Detail Log Aktivitas';
        return view('log_aktivitas.show', compact('item', 'title'));
    }

    public function edit($id)
    {
        $item = LogAktifitas::findOrFail($id);
        $users = User::orderBy('name')->get();
        $title = 'Edit Log Aktivitas';
        return view('log_aktivitas.edit', compact('item', 'users', 'title'));
    }

    public function update(Request $request, $id)
    {
        $item = LogAktifitas::findOrFail($id);

        $data = $request->validate([
            'id_user' => 'required|exists:tb_user,id',
            'aktivitas' => 'required|string|max:255',
            'waktu_aktivitas' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $item->update($data);
        return redirect()->route('log-aktivitas.index')->with('success','Log aktivitas berhasil diupdate');
    }

    public function destroy($id)
    {
        LogAktifitas::destroy($id);
        return redirect()->route('log-aktivitas.index')->with('success','Log aktivitas berhasil dihapus');
    }

    public function deleteAll()
    {
        LogAktifitas::truncate();
        return redirect()->route('log-aktivitas.index')->with('success','Semua log aktivitas berhasil dihapus');
    }
}

