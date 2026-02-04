<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PetugasDashboardController extends Controller
{
    public function index()
    {
        return view('petugas.dashboard');
    }
}
