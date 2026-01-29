<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogAktifitas;
use App\Models\User;

class LogAktifitasController extends Controller
{
    public function index()
    {
        $items = LogAktifitas::with('user')->orderBy('id_log','desc')->paginate(15);
        $title = 'Data Log Aktivitas';
        return view('log_aktivitas.index', compact('items', 'title'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
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
}

