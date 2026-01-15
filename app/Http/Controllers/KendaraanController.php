<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\User;

class KendaraanController extends Controller
{
    public function index()
    {
        $items = Kendaraan::orderBy('id_kendaraan','desc')->paginate(15);
        return view('kendaraan.index', compact('items'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('kendaraan.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'jenis_kendaraan' => 'required|string|max:20',
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:100',
            'id_user' => 'required|integer',
        ]);

        Kendaraan::create($data);

        return redirect()->route('kendaraan.index')->with('success','Kendaraan added');
    }

    public function edit($id)
    {
        $item = Kendaraan::findOrFail($id);
        $users = User::orderBy('name')->get();
        return view('kendaraan.edit', compact('item','users'));
    }

    public function update(Request $request, $id)
    {
        $item = Kendaraan::findOrFail($id);
        $data = $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'jenis_kendaraan' => 'required|string|max:20',
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:100',
            'id_user' => 'required|integer',
        ]);

        $item->update($data);

        return redirect()->route('kendaraan.index')->with('success','Kendaraan updated');
    }

    public function destroy($id)
    {
        Kendaraan::destroy($id);
        return redirect()->route('kendaraan.index')->with('success','Kendaraan deleted');
    }
}

