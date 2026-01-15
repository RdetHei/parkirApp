<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AreaParkir;

class AreaParkirController extends Controller
{
    public function index()
    {
        $areas = AreaParkir::orderBy('id_area','desc')->paginate(15);
        return view('area_parkir.index', compact('areas'));
    }

    public function create()
    {
        return view('area_parkir.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_area' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
        ]);

        AreaParkir::create($data);

        return redirect()->route('area-parkir.index')->with('success','Area created');
    }

    public function edit($id)
    {
        $area = AreaParkir::findOrFail($id);
        return view('area_parkir.edit', compact('area'));
    }

    public function update(Request $request, $id)
    {
        $area = AreaParkir::findOrFail($id);
        $data = $request->validate([
            'nama_area' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
        ]);
        $area->update($data);
        return redirect()->route('area-parkir.index')->with('success','Area updated');
    }

    public function destroy($id)
    {
        AreaParkir::destroy($id);
        return redirect()->route('area-parkir.index')->with('success','Area deleted');
    }
}

