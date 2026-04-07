<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\ParkingMapSlot;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $total_slots = ParkingMapSlot::count();
        $total_areas = AreaParkir::count();
        $total_vehicles = Transaksi::count();

        return view('layouts.landing', compact('total_slots', 'total_areas', 'total_vehicles'));
    }
}
