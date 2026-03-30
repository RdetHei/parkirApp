<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\User;
use App\Support\PlatNomorNormalizer;

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
            'id_user' => 'nullable|exists:tb_user,id',
        ]);

        $platNormalized = PlatNomorNormalizer::normalize($data['plat_nomor']);
        if (Kendaraan::where('plat_nomor', $platNormalized)->exists()) {
            return back()->withInput()->with('error', 'Plat nomor ini sudah ada.');
        }

        $data['plat_nomor'] = $platNormalized;
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
            'id_user' => 'nullable|exists:tb_user,id',
        ]);

        $platNormalized = PlatNomorNormalizer::normalize($data['plat_nomor']);
        $exists = Kendaraan::where('plat_nomor', $platNormalized)
            ->where('id_kendaraan', '!=', $item->id_kendaraan)
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Plat nomor ini sudah ada.');
        }

        $data['plat_nomor'] = $platNormalized;
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

