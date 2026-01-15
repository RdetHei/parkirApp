<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParkirController extends Controller
{
    /**
     * Display a listing of parked items.
     */
    public function index()
    {
        return view('parkir.index');
    }

    /**
     * Show the form for creating a new parkir entry.
     */
    public function create()
    {
        return view('parkir.create');
    }

    /**
     * Store a newly created parkir entry.
     */
    public function store(Request $request)
    {
        $request->validate([
            // add validation rules as needed
        ]);

        // TODO: implement storing logic (model creation)

        return redirect()->route('parkir.index')->with('success', 'Entry created.');
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            // add validation rules as needed
        ]);

        // TODO: implement update logic

        return redirect()->route('parkir.index')->with('success', 'Entry updated.');
    }

    /**
     * Print/preview a receipt for the given entry.
     */
    public function print($id)
    {
        return view('parkir.receipt', compact('id'));
    }
}
