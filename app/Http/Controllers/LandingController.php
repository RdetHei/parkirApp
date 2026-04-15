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
        // Cache stats for 1 minute for more real-time feeling
        // Change key to landing_stats_v3 to force refresh after structure change
        $stats = Cache::remember('landing_stats_v3', 60, function () {
            $defaultArea = AreaParkir::where('is_default_map', true)->first() ?? AreaParkir::first();

            return [
                'total_slots' => ParkingMapSlot::count(),
                'total_areas' => AreaParkir::count(),
                'total_vehicles' => Transaksi::count(),
                'default_area' => $defaultArea,
                'recent_activities' => Transaksi::with(['kendaraan', 'area'])
                    ->whereIn('status', ['masuk', 'keluar'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(3)
                    ->get()
            ];
        });

        return view('layouts.landing', [
            'total_slots' => $stats['total_slots'] ?? 0,
            'total_areas' => $stats['total_areas'] ?? 0,
            'total_vehicles' => $stats['total_vehicles'] ?? 0,
            'default_area' => $stats['default_area'] ?? null,
            'recent_activities' => $stats['recent_activities'] ?? collect([]),
        ]);
    }
}
