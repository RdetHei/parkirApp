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
            'plat_nomor' => 'required|string|max:15|unique:tb_kendaraan,plat_nomor',
            'jenis_kendaraan' => 'required|string|max:20',
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:100',
            'id_user' => 'required|exists:tb_user,id',
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
            'plat_nomor' => 'required|string|max:15|unique:tb_kendaraan,plat_nomor,' . $id . ',id_kendaraan',
            'jenis_kendaraan' => 'required|string|max:20',
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:100',
            'id_user' => 'required|exists:tb_user,id',
        ]);

        $item->update($data);

        return redirect()->route('kendaraan.index')->with('success','Kendaraan updated');
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        // Cek apakah kendaraan masih digunakan di transaksi
        if ($kendaraan->transaksis()->exists()) {
            return redirect()->route('kendaraan.index')->with('error', 'Kendaraan tidak dapat dihapus karena masih digunakan dalam transaksi.');
        }

        $kendaraan->delete();
        
        return redirect()->route('kendaraan.index')->with('success','Kendaraan deleted');
    }
}

