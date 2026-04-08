<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\ParkingMapSlot;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    public function index()
    {
        // Cache stats for 10 minutes (600 seconds)
        $stats = Cache::remember('landing_stats', 600, function () {
            return [
                'total_slots' => ParkingMapSlot::count(),
                'total_areas' => AreaParkir::count(),
                'total_vehicles' => Transaksi::count(),
            ];
        });

        return view('layouts.landing', [
            'total_slots' => $stats['total_slots'],
            'total_areas' => $stats['total_areas'],
            'total_vehicles' => $stats['total_vehicles'],
        ]);
    }
}
