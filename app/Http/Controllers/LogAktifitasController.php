<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogAktifitas;

class LogAktifitasController extends Controller
{
    public function index()
    {
        $items = LogAktifitas::orderBy('id_log','desc')->paginate(15);
        return view('log_aktivitas.index', compact('items'));
    }

    public function create()
    {
        return view('log_aktivitas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_user' => 'required|integer',
            'aktivitas' => 'required|string|max:100',
            'waktu_aktivitas' => 'required|date',
        ]);

        LogAktifitas::create($data);
        return redirect()->route('log-aktivitas.index')->with('success','Log created');
    }

    public function destroy($id)
    {
        LogAktifitas::destroy($id);
        return redirect()->route('log-aktivitas.index')->with('success','Log deleted');
    }
}

