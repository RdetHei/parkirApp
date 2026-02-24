<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingMap;
use App\Models\ParkingMapSlot;
use App\Models\ParkingMapCamera;
use App\Models\Transaksi;
use Carbon\Carbon;

class ParkingSlotController extends Controller
{
    /**
     * API: slot parkir + kamera di peta + ringkasan (untuk Leaflet).
     */
    public function index(Request $request)
    {
        $mapId = $request->query('map_id');
        $map = null;

        if ($mapId) {
            $map = ParkingMap::find((int) $mapId);
        }
        if (!$map) {
            $map = ParkingMap::getDefaultOrFirst();
        }

        $slots = [];
        $cameras = [];
        $summary = ['total' => 0, 'empty' => 0, 'occupied' => 0, 'reserved' => 0];

        if ($map) {
            $slotModels = ParkingMapSlot::where('parking_map_id', $map->id)
                ->with(['areaParkir', 'camera'])
                ->orderBy('code')
                ->get();

            $slotIds = $slotModels->pluck('id')->all();
            $areaIds = $slotModels->pluck('area_parkir_id')->filter()->unique()->values()->all();
            $activeBySlot = $this->getActiveTransactionsBySlot($slotIds);
            $activeByArea = $this->getActiveTransactionsByArea($areaIds);

            foreach ($slotModels as $slot) {
                $status = 'empty';
                $vehiclePlate = null;
                $areaName = $slot->areaParkir?->nama_area ?? null;

                if (isset($activeBySlot[$slot->id])) {
                    $tx = $activeBySlot[$slot->id];
                    $status = $tx['status'] === 'bookmarked' ? 'reserved' : 'occupied';
                    $vehiclePlate = $tx['vehicle_plate'] ?? null;
                } elseif ($slot->area_parkir_id && isset($activeByArea[$slot->area_parkir_id])) {
                    $tx = $activeByArea[$slot->area_parkir_id];
                    $status = $tx['status'] === 'bookmarked' ? 'reserved' : 'occupied';
                    $vehiclePlate = $tx['vehicle_plate'] ?? null;
                }

                $slots[] = [
                    'id' => $slot->id,
                    'code' => $slot->code,
                    'x' => (int) $slot->x,
                    'y' => (int) $slot->y,
                    'width' => (int) $slot->width,
                    'height' => (int) $slot->height,
                    'status' => $status,
                    'vehicle_plate' => $vehiclePlate,
                    'area_name' => $areaName,
                    'notes' => $slot->notes,
                    'camera_id' => $slot->camera_id,
                    'meta' => $slot->meta,
                ];

                $summary['total']++;
                if ($status === 'empty') {
                    $summary['empty']++;
                } elseif ($status === 'occupied') {
                    $summary['occupied']++;
                } else {
                    $summary['reserved']++;
                }
            }

            $mapCameras = ParkingMapCamera::where('parking_map_id', $map->id)
                ->with('camera')
                ->get();

            foreach ($mapCameras as $pmc) {
                if (!$pmc->camera) {
                    continue;
                }
                $cameras[] = [
                    'id' => $pmc->camera->id,
                    'name' => $pmc->camera->nama,
                    'type' => $pmc->camera->tipe,
                    'url' => $pmc->camera->url,
                    'x' => (int) $pmc->x,
                    'y' => (int) $pmc->y,
                ];
            }
        }

        return response()->json([
            'slots' => $slots,
            'cameras' => $cameras,
            'summary' => $summary,
        ]);
    }

    /** Transaksi aktif per slot (parking_map_slot_id). */
    private function getActiveTransactionsBySlot(array $slotIds): array
    {
        if (empty($slotIds)) {
            return [];
        }
        $tenMinutesAgo = Carbon::now()->subMinutes(10);
        $transaksis = Transaksi::whereIn('parking_map_slot_id', $slotIds)
            ->whereNotNull('parking_map_slot_id')
            ->where(function ($q) use ($tenMinutesAgo) {
                $q->where(function ($q2) {
                    $q2->whereNull('waktu_keluar')->where('status', 'masuk');
                })->orWhere(function ($q2) use ($tenMinutesAgo) {
                    $q2->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
                });
            })
            ->with(['kendaraan'])
            ->get();
        $bySlot = [];
        foreach ($transaksis as $tx) {
            $bySlot[$tx->parking_map_slot_id] = [
                'status' => $tx->status,
                'vehicle_plate' => $tx->kendaraan?->plat_nomor ?? null,
            ];
        }
        return $bySlot;
    }

    private function getActiveTransactionsByArea(array $areaIds): array
    {
        if (empty($areaIds)) {
            return [];
        }
        $tenMinutesAgo = Carbon::now()->subMinutes(10);
        $transaksis = Transaksi::whereIn('id_area', $areaIds)
            ->whereNull('parking_map_slot_id')
            ->where(function ($q) use ($tenMinutesAgo) {
                $q->where(function ($q2) {
                    $q2->whereNull('waktu_keluar')->where('status', 'masuk');
                })->orWhere(function ($q2) use ($tenMinutesAgo) {
                    $q2->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
                });
            })
            ->with(['kendaraan'])
            ->get();
        $byArea = [];
        foreach ($transaksis as $tx) {
            $byArea[$tx->id_area] = [
                'status' => $tx->status,
                'vehicle_plate' => $tx->kendaraan?->plat_nomor ?? null,
            ];
        }
        return $byArea;
    }

    /** API: daftar slot untuk area (untuk dropdown form catat masuk). */
    public function slotsByArea(\App\Models\AreaParkir $area)
    {
        $slots = ParkingMapSlot::where('area_parkir_id', $area->id_area)
            ->orderBy('code')
            ->get(['id', 'code', 'area_parkir_id']);

        $slotIds = $slots->pluck('id')->all();
        $occupiedSlotIds = [];
        if (!empty($slotIds)) {
            $occupiedSlotIds = Transaksi::whereIn('parking_map_slot_id', $slotIds)
                ->whereNull('waktu_keluar')
                ->where('status', 'masuk')
                ->pluck('parking_map_slot_id')
                ->all();
        }

        $list = $slots->map(function ($s) use ($occupiedSlotIds) {
            return [
                'id' => $s->id,
                'code' => $s->code,
                'occupied' => in_array($s->id, $occupiedSlotIds, true),
            ];
        });

        return response()->json($list);
    }

    public function view(Request $request)
    {
        $maps = ParkingMap::orderBy('name')->get();

        $selectedId = $request->query('map');
        $map = null;

        if ($selectedId) {
            $map = $maps->firstWhere('id', (int) $selectedId);
        }

        if (!$map) {
            $map = ParkingMap::getDefaultOrFirst();
        }

        return view('parking-map', [
            'map' => $map,
            'maps' => $maps,
        ]);
    }
}
