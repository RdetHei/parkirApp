<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AreaParkir;
use App\Models\ParkingMapSlot;
use App\Models\ParkingMapCamera;
use App\Models\ParkingSlotReservation;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Kendaraan;
use App\Models\Tarif;

class ParkingSlotController extends Controller
{
    /**
     * User: Booking slot (area-based bookmark)
     */
    public function bookingPage(Request $request, $areaId = null)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $selectedDaerah = $request->query('daerah');
        $daerahs = AreaParkir::query()
            ->whereNotNull('daerah')
            ->where('daerah', '!=', '')
            ->orderBy('daerah')
            ->distinct()
            ->pluck('daerah');

        $query = AreaParkir::orderBy('nama_area');
        if ($selectedDaerah) {
            $query->where('daerah', $selectedDaerah);
        }
        $areas = $query->get();

        // Pilih peta area aktif berdasarkan filter + pilihan user.
        $map = null;
        if ($areaId) {
            $map = $areas->firstWhere('id_area', (int) $areaId) ?? AreaParkir::find((int) $areaId);
        }
        if (!$map) {
            $map = $areas->first() ?? AreaParkir::getDefaultMap();
        }
        if ($selectedDaerah && $map && !$areas->contains('id_area', $map->id_area)) {
            $map = $areas->first();
        }
        $selectedAreaId = $map?->id_area;

        $kendaraans = Kendaraan::where('id_user', $user->id)->orderBy('plat_nomor')->get();
        $tarifs = Tarif::orderBy('jenis_kendaraan')->get();
        $activeBookingInfo = null;

        // Hitung status per area (kosong / terisi / dibookmark)
        $now = Carbon::now();
        $statusPerArea = [];
        $myBookingIds = [];

        foreach ($areas as $area) {
            $occupied = Transaksi::where('id_area', $area->id_area)
                ->whereNull('waktu_keluar')
                ->where('status', 'masuk')
                ->exists();

            if ($occupied) {
                $statusPerArea[$area->id_area] = 'occupied';
                continue;
            }

            $reservation = ParkingSlotReservation::active()
                ->where('id_area', $area->id_area)
                ->orderByDesc('expires_at')
                ->first();

            if ($reservation) {
                if ((int) $reservation->id_user === (int) $user->id) {
                    $statusPerArea[$area->id_area] = 'bookmarked-by-me';
                    $myBookingIds[$area->id_area] = $reservation->id;
                } else {
                    $statusPerArea[$area->id_area] = 'bookmarked';
                }
                continue;
            }

            $legacy = Transaksi::where('id_area', $area->id_area)
                ->where('status', 'bookmarked')
                ->where('bookmarked_at', '>', $now->copy()->subMinutes(10))
                ->orderByDesc('bookmarked_at')
                ->first();

            if ($legacy) {
                if ((int) $legacy->id_user === (int) $user->id) {
                    $statusPerArea[$area->id_area] = 'bookmarked-by-me';
                    $myBookingIds[$area->id_area] = $legacy->id_parkir;
                } else {
                    $statusPerArea[$area->id_area] = 'bookmarked';
                }
                continue;
            }

            $statusPerArea[$area->id_area] = 'empty';
        }

        $activeReservation = ParkingSlotReservation::active()
            ->with(['area:id_area,nama_area', 'slot:id,code'])
            ->where('id_user', $user->id)
            ->latest('expires_at')
            ->first();

        if ($activeReservation) {
            $activeBookingInfo = [
                'id' => (int) $activeReservation->id,
                'kind' => 'reservation',
                'area_name' => $activeReservation->area?->nama_area ?? '-',
                'slot_code' => $activeReservation->slot?->code ?? '-',
                'expires_at' => optional($activeReservation->expires_at)->toIso8601String(),
            ];
        } else {
            $legacyBooking = Transaksi::where('id_user', $user->id)
                ->where('status', 'bookmarked')
                ->where('bookmarked_at', '>', $now->copy()->subMinutes(10))
                ->with(['area:id_area,nama_area', 'parkingMapSlot:id,code'])
                ->latest('bookmarked_at')
                ->first();

            if ($legacyBooking) {
                $activeBookingInfo = [
                    'id' => (int) $legacyBooking->id_parkir,
                    'kind' => 'transaksi',
                    'area_name' => $legacyBooking->area?->nama_area ?? '-',
                    'slot_code' => $legacyBooking->parkingMapSlot?->code ?? '-',
                    'expires_at' => optional($legacyBooking->bookmarked_at)->addMinutes(10)->toIso8601String(),
                ];
            }
        }

        return view('user.bookings', compact('areas', 'statusPerArea', 'map', 'myBookingIds', 'kendaraans', 'tarifs', 'daerahs', 'selectedDaerah', 'selectedAreaId', 'activeBookingInfo'));
    }

    /**
     * API: slot parkir + kamera di peta + ringkasan (untuk Leaflet).
     */
    public function index(Request $request)
    {
        $areaId = $request->query('map_id'); // map_id parameter is now area_id
        $area = null;

        if ($areaId) {
            $area = AreaParkir::find((int) $areaId);
        }
        if (!$area) {
            $area = AreaParkir::getDefaultMap();
        }

        $slots = [];
        $cameras = [];
        $summary = ['total' => 0, 'empty' => 0, 'occupied' => 0, 'reserved' => 0, 'unassigned' => 0];

        if ($area) {
            $currentUserId = Auth::user()?->id ?? null;
            $slotModels = ParkingMapSlot::where('area_parkir_id', $area->id_area)
                ->with(['areaParkir', 'camera'])
                ->orderBy('code')
                ->get();

            $slotIds = $slotModels->pluck('id')->all();
            $areaIds = [$area->id_area];
            $activeBySlot = $this->getActiveTransactionsBySlot($slotIds);
            $activeReservationsBySlot = $this->getActiveReservationsBySlot($slotIds);

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

            foreach ($slotModels as $slot) {
                $status = 'empty';
                $vehiclePlate = null;
                $areaName = $slot->areaParkir?->nama_area ?? null;
                $transaksiId = null;
                $isMineFlag = false;

                if (isset($activeBySlot[$slot->id])) {
                    $tx = $activeBySlot[$slot->id];
                    $isMine = $currentUserId && (int) $tx['user_id'] === (int) $currentUserId;
                    if ($tx['status'] === 'bookmarked') {
                        $status = $isMine ? 'reserved-by-me' : 'reserved';
                    } else {
                        $status = 'occupied';
                    }
                    $vehiclePlate = $tx['vehicle_plate'] ?? null;
                    $transaksiId = $tx['transaksi_id'] ?? null;
                    $isMineFlag = $isMine;
                } elseif (isset($activeReservationsBySlot[$slot->id])) {
                    $rsv = $activeReservationsBySlot[$slot->id];
                    $isMine = $currentUserId && (int) $rsv['user_id'] === (int) $currentUserId;
                    $status = $isMine ? 'reserved-by-me' : 'reserved';
                    $vehiclePlate = $rsv['vehicle_plate'] ?? null;
                    $transaksiId = $rsv['reservation_id'] ?? null;
                    $isMineFlag = $isMine;
                }

                $slots[] = [
                    'id' => $slot->id,
                    'code' => $slot->code,
                    'x' => (int) $slot->x,
                    'y' => (int) $slot->y,
                    'width' => (int) $slot->width,
                    'height' => (int) $slot->height,
                    'status' => $status,
                    'is_mine' => $isMineFlag ?? false,
                    'vehicle_plate' => $vehiclePlate,
                    'area_name' => $areaName,
                    'notes' => $slot->notes,
                    'camera_id' => $slot->camera_id,
                    'meta' => $slot->meta,
                    'area_id' => $slot->area_parkir_id,
                    'transaksi_id' => $transaksiId,
                ];

                $summary['total']++;
                if ($status === 'empty') $summary['empty']++;
                elseif ($status === 'occupied') $summary['occupied']++;
                else $summary['reserved']++;
            }

            $cameraModels = ParkingMapCamera::where('area_parkir_id', $area->id_area)
                ->with('camera')
                ->get();

            foreach ($cameraModels as $mc) {
                $cameras[] = [
                    'id' => $mc->camera_id,
                    'name' => $mc->camera?->nama ?? 'Unknown',
                    'x' => (int) $mc->x,
                    'y' => (int) $mc->y,
                    'stream_url' => $mc->camera?->url_stream,
                ];
            }
        }

        return response()->json([
            'map' => $area ? [
                'id' => $area->id_area,
                'name' => $area->nama_area,
                'image' => $area->map_image_url,
                'width' => (int) $area->map_width,
                'height' => (int) $area->map_height,
            ] : null,
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
            // `parking_map_id` sudah dihapus (area adalah map).
            ->get(['id', 'code', 'area_parkir_id']);

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
        $areaId = $request->query('map_id');
        $area = $areaId ? AreaParkir::find($areaId) : AreaParkir::getDefaultMap();
        $maps = AreaParkir::whereNotNull('map_image')->where('map_image', '!=', '')->orderBy('nama_area')->get();
        $title = 'Peta Parkir';

        return view('parking-map', compact('area', 'maps', 'title'));
    }

    public function bookmark(Request $request, AreaParkir $area)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $now = Carbon::now();
        $tenMinutesAgo = $now->copy()->subMinutes(10);

        // Check if user already has an active booking in this area or any area
        $existing = Transaksi::where('id_user', $user->id)
            ->where('status', 'bookmarked')
            ->where('bookmarked_at', '>', $tenMinutesAgo)
            ->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda sudah memiliki booking aktif yang berlaku selama 10 menit.'], 400);
            }
            return redirect()->back()->with('error', 'Anda sudah memiliki booking aktif yang berlaku selama 10 menit.');
        }

        $existingReservation = ParkingSlotReservation::where('id_user', $user->id)
            ->active()
            ->first();

        if ($existingReservation) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda sudah memiliki reservasi aktif.'], 400);
            }
            return redirect()->back()->with('error', 'Anda sudah memiliki reservasi aktif.');
        }

        // Pick a slot if parking_map_slot_id is provided, or pick the first empty one in the area
        $slotId = $request->input('parking_map_slot_id') ?? $request->input('slot_id');
        $slot = null;

        if ($slotId) {
            $slot = ParkingMapSlot::find($slotId);
            if (!$slot || $slot->area_parkir_id !== $area->id_area) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Slot tidak ditemukan di area ini.'], 404);
                }
                return redirect()->back()->with('error', 'Slot tidak ditemukan di area ini.');
            }
        } else {
            // Find first empty slot
            $occupiedSlotIds = Transaksi::where('id_area', $area->id_area)
                ->where(function ($q) use ($tenMinutesAgo) {
                    $q->where(function ($q2) {
                        $q2->whereNull('waktu_keluar')->where('status', 'masuk');
                    })->orWhere(function ($q2) use ($tenMinutesAgo) {
                        $q2->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
                    });
                })
                ->pluck('parking_map_slot_id')
                ->filter()
                ->all();

            $reservedSlotIds = ParkingSlotReservation::active()
                ->where('id_area', $area->id_area)
                ->pluck('parking_map_slot_id')
                ->filter()
                ->all();

            $blockedIds = array_unique(array_merge($occupiedSlotIds, $reservedSlotIds));

            $slot = ParkingMapSlot::where('area_parkir_id', $area->id_area)
                ->whereNotIn('id', $blockedIds)
                ->first();
        }

        if (!$slot) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Maaf, area ini sudah penuh.'], 400);
            }
            return redirect()->back()->with('error', 'Maaf, area ini sudah penuh.');
        }

        $idTarif = $request->input('id_tarif');

        // Jika id_tarif tidak dikirim (Otomatis), cari berdasarkan jenis kendaraan
        if (!$idTarif && $request->input('id_kendaraan')) {
            $kendaraan = \App\Models\Kendaraan::find($request->input('id_kendaraan'));
            if ($kendaraan) {
                $tarif = \App\Models\Tarif::where('jenis_kendaraan', $kendaraan->jenis_kendaraan)->first();
                if ($tarif) {
                    $idTarif = $tarif->id_tarif;
                }
            }
        }

        // Fallback jika masih null (untuk menghindari SQL error)
        if (!$idTarif) {
            $idTarif = \App\Models\Tarif::first()?->id_tarif;
        }

        // Create booking (using Transaksi with status bookmarked for simplicity with current UI)
        $transaksi = Transaksi::create([
            'id_user' => $user->id,
            'id_area' => $area->id_area,
            'parking_map_slot_id' => $slot->id,
            'id_kendaraan' => $request->input('id_kendaraan'),
            'id_tarif' => $idTarif,
            'status' => 'bookmarked',
            'bookmarked_at' => $now,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Booking berhasil!',
                'transaksi' => $transaksi
            ]);
        }

        return redirect()->back()->with('success', 'Booking berhasil!');
    }

    public function unbookmark(Request $request, $id)
    {
        $user = Auth::user();

        // Try finding as Transaksi first
        $transaksi = Transaksi::where('id_parkir', $id)
            ->where('id_user', $user->id)
            ->where('status', 'bookmarked')
            ->first();

        if ($transaksi) {
            $transaksi->delete();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Booking dibatalkan.']);
            }
            return redirect()->back()->with('success', 'Booking dibatalkan.');
        }

        // Try finding as Reservation
        $reservation = ParkingSlotReservation::where('id', $id)
            ->where('id_user', $user->id)
            ->first();

        if ($reservation) {
            $reservation->delete();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Reservasi dibatalkan.']);
            }
            return redirect()->back()->with('success', 'Reservasi dibatalkan.');
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Data booking tidak ditemukan.'], 404);
        }
        return redirect()->back()->with('error', 'Data booking tidak ditemukan.');
    }
}
