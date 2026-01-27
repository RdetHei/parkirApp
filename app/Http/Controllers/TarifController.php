<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarif;

class TarifController extends Controller
{
    public function index()
    {
        $items = Tarif::orderBy('id_tarif','desc')->paginate(15);
        return view('tarif.index', compact('items'));
    }

    public function create()
    {
        return view('tarif.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
            'tarif_perjam' => 'required|numeric|min:0',
        ]);

        Tarif::create($data);
        return redirect()->route('tarif.index')->with('success','Tarif added');
    }

    public function edit($id)
    {
        $item = Tarif::findOrFail($id);
        return view('tarif.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Tarif::findOrFail($id);
        $data = $request->validate([
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
            'tarif_perjam' => 'required|numeric|min:0',
        ]);
        $item->update($data);
        return redirect()->route('tarif.index')->with('success','Tarif updated');
    }

    public function destroy($id)
    {
        $tarif = Tarif::findOrFail($id);

        // Cek apakah tarif masih digunakan di transaksi
        if ($tarif->transaksis()->exists()) {
            return redirect()->route('tarif.index')->with('error', 'Tarif tidak dapat dihapus karena masih digunakan dalam transaksi.');
        }

        $tarif->delete();

        return redirect()->route('tarif.index')->with('success','Tarif deleted');
    }
}

