<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingMap;
use App\Models\ParkingMapSlot;
use App\Models\ParkingMapCamera;
use App\Models\ParkingSlotReservation;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        $summary = ['total' => 0, 'empty' => 0, 'occupied' => 0, 'reserved' => 0, 'unassigned' => 0];

        if ($map) {
            $currentUserId = Auth::user()?->id ?? null;
            $slotModels = ParkingMapSlot::where('parking_map_id', $map->id)
                ->with(['areaParkir', 'camera'])
                ->orderBy('code')
                ->get();

            $slotIds = $slotModels->pluck('id')->all();
            $areaIds = $slotModels->pluck('area_parkir_id')->filter()->unique()->values()->all();
            $activeBySlot = $this->getActiveTransactionsBySlot($slotIds);
            $activeReservationsBySlot = $this->getActiveReservationsBySlot($slotIds);
            if (!empty($areaIds)) {
                $tenMinutesAgo = Carbon::now()->subMinutes(10);
                $summary['unassigned'] = Transaksi::whereIn('id_area', $areaIds)
                    ->whereNull('parking_map_slot_id')
                    ->where(function ($q) use ($tenMinutesAgo) {
                        $q->where(function ($q2) {
                            $q2->whereNull('waktu_keluar')->where('status', 'masuk');
                        })->orWhere(function ($q2) use ($tenMinutesAgo) {
                            $q2->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
                        });
                    })
                    ->count();
            }

            foreach ($slotModels as $slot) {
                $status = 'empty';
                $vehiclePlate = null;
                $areaName = $slot->areaParkir?->nama_area ?? null;
                $transaksiId = null;

                if (isset($activeBySlot[$slot->id])) {
                    $tx = $activeBySlot[$slot->id];
                    $isMine = $currentUserId && $tx['user_id'] === $currentUserId;
                    if ($tx['status'] === 'bookmarked') {
                        $status = $isMine ? 'reserved-by-me' : 'reserved';
                    } else {
                        $status = 'occupied';
                    }
                    $vehiclePlate = $tx['vehicle_plate'] ?? null;
                    $transaksiId = $tx['transaksi_id'] ?? null;
                } elseif (isset($activeReservationsBySlot[$slot->id])) {
                    $rsv = $activeReservationsBySlot[$slot->id];
                    $isMine = $currentUserId && $rsv['user_id'] === $currentUserId;
                    $status = $isMine ? 'reserved-by-me' : 'reserved';
                    $vehiclePlate = $rsv['vehicle_plate'] ?? null;
                    $transaksiId = $rsv['reservation_id'] ?? null;
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
                    'area_id' => $slot->area_parkir_id,
                    'transaksi_id' => $transaksiId,
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
                'user_id' => $tx->id_user,
                'transaksi_id' => $tx->id_parkir,
            ];
        }
        return $bySlot;
    }

    private function getActiveReservationsBySlot(array $slotIds): array
    {
        if (empty($slotIds)) {
            return [];
        }

        $reservations = ParkingSlotReservation::active()
            ->whereIn('parking_map_slot_id', $slotIds)
            ->with(['kendaraan'])
            ->get();

        $bySlot = [];
        foreach ($reservations as $r) {
            $bySlot[$r->parking_map_slot_id] = [
                'user_id' => $r->id_user,
                'vehicle_plate' => $r->kendaraan?->plat_nomor ?? null,
                'reservation_id' => $r->id,
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
        $slots = ParkingMapSlot::forArea($area)
            ->orderBy('code')
            ->get(['id', 'code', 'area_parkir_id', 'parking_map_id']);

        $slotIds = $slots->pluck('id')->all();
        $blockedSlotIds = [];
        if (!empty($slotIds)) {
            $tenMinutesAgo = Carbon::now()->subMinutes(10);
            $txBlocked = Transaksi::whereIn('parking_map_slot_id', $slotIds)
                ->whereNotNull('parking_map_slot_id')
                ->where(function ($q) use ($tenMinutesAgo) {
                    $q->where(function ($q2) {
                        $q2->whereNull('waktu_keluar')->where('status', 'masuk');
                    })->orWhere(function ($q2) use ($tenMinutesAgo) {
                        $q2->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
                    });
                })
                ->pluck('parking_map_slot_id')
                ->all();

            $rsvBlocked = ParkingSlotReservation::active()
                ->whereIn('parking_map_slot_id', $slotIds)
                ->pluck('parking_map_slot_id')
                ->all();

            $blockedSlotIds = array_values(array_unique(array_merge($txBlocked, $rsvBlocked)));
        }

        $list = $slots->map(function ($s) use ($blockedSlotIds) {
            return [
                'id' => $s->id,
                'code' => $s->code,
                'occupied' => in_array($s->id, $blockedSlotIds, true),
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
