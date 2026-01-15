<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\User;
use App\Models\AreaParkir;

class TransaksiController extends Controller
{
    public function index()
    {
        $items = Transaksi::orderBy('id_parkit','desc')->paginate(15);
        return view('transaksi.index', compact('items'));
    }

    public function create()
    {
        $kendaraans = Kendaraan::orderBy('plat_nomor')->get();
        $tarifs = Tarif::orderBy('jenis_kendaraan')->get();
        $users = User::orderBy('name')->get();
        $areas = AreaParkir::orderBy('nama_area')->get();
        return view('transaksi.create', compact('kendaraans','tarifs','users','areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kendaraan' => 'required|integer',
            'waktu_masuk' => 'required|date',
            'id_tarif' => 'required|integer',
            'id_user' => 'required|integer',
            'id_area' => 'required|integer',
            'status' => 'required|in:masuk,keluar',
        ]);

        Transaksi::create($data);
        return redirect()->route('transaksi.index')->with('success','Transaksi created');
    }

    public function edit($id)
    {
        $item = Transaksi::findOrFail($id);
        $kendaraans = Kendaraan::orderBy('plat_nomor')->get();
        $tarifs = Tarif::orderBy('jenis_kendaraan')->get();
        $users = User::orderBy('name')->get();
        $areas = AreaParkir::orderBy('nama_area')->get();
        return view('transaksi.edit', compact('item','kendaraans','tarifs','users','areas'));
    }

    public function update(Request $request, $id)
    {
        $item = Transaksi::findOrFail($id);
        $data = $request->validate([
            'waktu_keluar' => 'nullable|date',
            'durasi_jam' => 'nullable|integer',
            'biaya_total' => 'nullable|numeric',
            'status' => 'required|in:masuk,keluar',
        ]);

        $item->update($data);
        return redirect()->route('transaksi.index')->with('success','Transaksi updated');
    }

    public function destroy($id)
    {
        Transaksi::destroy($id);
        return redirect()->route('transaksi.index')->with('success','Transaksi deleted');
    }
}

