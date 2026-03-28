<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AreaParkir;
use App\Models\ParkingSlotReservation;
use App\Models\Transaksi;
use App\Models\ParkingMapSlot;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\DB;

class ParkingMapController extends Controller
{
    public function index()
    {
        $parkingAreas = AreaParkir::with(['transaksis' => function($query) {
            // Fetch active 'masuk' transactions and unexpired 'bookmarked' transactions
            $query->where(function($q) {
                $q->whereNull('waktu_keluar') // Active 'masuk'
                  ->where('status', 'masuk');
            })->orWhere(function($q) {
                $q->where('status', 'bookmarked')
                  ->where('bookmarked_at', '>', Carbon::now()->subMinutes(10)); // Unexpired bookmarked
            });
        }, 'transaksis.kendaraan', 'transaksis.user'])->get();

        $mapData = $parkingAreas->map(function($area) {
            $status = 'empty'; // Default status
            $vehicle = null;
            $bookmarkedBy = null;
            $bookmarkedAt = null;

            // Priority: Occupied -> Bookmarked -> Empty
            $occupiedTransaction = $area->transaksis->firstWhere('status', 'masuk');
            $bookmarkedTransaction = $area->transaksis->firstWhere('status', 'bookmarked');

            if ($occupiedTransaction) {
                $status = 'occupied';
                $vehicle = [
                    'plat_nomor' => $occupiedTransaction->kendaraan->plat_nomor ?? 'N/A',
                    'jenis_kendaraan' => $occupiedTransaction->kendaraan->jenis_kendaraan ?? 'N/A',
                    'waktu_masuk' => $occupiedTransaction->waktu_masuk->format('H:i') ?? 'N/A',
                    'operator' => $occupiedTransaction->user->name ?? 'N/A',
                    'id_transaksi' => $occupiedTransaction->id_parkir,
                ];
            } elseif ($bookmarkedTransaction) {
                $status = 'bookmarked';
                $bookmarkedBy = $bookmarkedTransaction->user->name ?? null;
                $bookmarkedAt = $bookmarkedTransaction->bookmarked_at;
                // Add id_transaksi for bookmarked slot to allow unbookmarking/check-in
                $vehicle = [
                    'id_transaksi' => $bookmarkedTransaction->id_parkir,
                    'operator' => $bookmarkedBy,
                    'bookmarked_at' => $bookmarkedAt,
                ];
            }
            // else status remains 'empty'

            return [
                'id' => $area->id_area,
                'name' => $area->nama_area,
                'capacity' => $area->kapasitas,
                'occupied_count' => $area->transaksis->where('status', 'masuk')->count(),
                'status' => $status,
                'vehicle' => $vehicle,
                'svg_coords' => [
                    'x' => 0, // Placeholder
                    'y' => 0, // Placeholder
                    'width' => 100, // Placeholder
                    'height' => 50, // Placeholder
                ]
            ];
        });

        return response()->json($mapData);
    }

    public function bookmark(Request $request, $area_id)
    {
        $area = AreaParkir::find($area_id);

        if (!$area) {
            return response()->json(['message' => 'Area parkir tidak ditemukan.'], 404);
        }

        $request->validate([
            'id_kendaraan' => 'required|exists:tb_kendaraan,id_kendaraan',
            'id_tarif' => 'nullable|exists:tb_tarif,id_tarif',
            'parking_map_slot_id' => 'nullable|exists:tb_parking_map_slots,id',
            'slot_code' => 'nullable|string|max:50',
        ]);

        // Untuk user: pastikan kendaraan milik user
        $kendaraan = \App\Models\Kendaraan::find($request->id_kendaraan);
        if (!$kendaraan || (int) $kendaraan->id_user !== (int) Auth::id()) {
            return response()->json(['message' => 'Kendaraan tidak valid untuk akun Anda.'], 403);
        }

        $tarifId = $request->input('id_tarif');
        if (!$tarifId) {
            $tarif = Tarif::where('jenis_kendaraan', $kendaraan->jenis_kendaraan)->orderBy('id_tarif')->first();
            if (!$tarif) {
                return response()->json(['message' => 'Tarif tidak ditemukan untuk jenis kendaraan ini.'], 422);
            }
            $tarifId = $tarif->id_tarif;
        }

        $slotId = $request->input('parking_map_slot_id');
        $slotCode = $request->input('slot_code');

        $expiresAt = now()->addMinutes(10);
        $reservedAt = now();

        try {
            $reservation = DB::transaction(function () use ($area, $kendaraan, $tarifId, $slotId, $slotCode, $reservedAt, $expiresAt) {
                $userId = (int) Auth::id();

                $existingMine = ParkingSlotReservation::active()
                    ->where('id_user', $userId)
                    ->lockForUpdate()
                    ->first();

                if ($existingMine) {
                    throw new \Exception('Anda masih memiliki booking aktif. Silakan batalkan terlebih dahulu.');
                }

                $tenMinutesAgo = Carbon::now()->subMinutes(10);

                if ($slotId) {
                    $slot = ParkingMapSlot::with('parkingMap')->lockForUpdate()->findOrFail($slotId);
                    if ((int) $slot->effectiveAreaId() !== (int) $area->id_area) {
                        throw new \Exception('Slot tidak valid untuk area ini.');
                    }
                } else {
                    $candidateSlots = ParkingMapSlot::forArea($area)
                        ->orderBy('code')
                        ->get(['id', 'code', 'area_parkir_id', 'parking_map_id']);

                    $slotIds = $candidateSlots->pluck('id')->all();
                    $blockedTx = [];
                    if (!empty($slotIds)) {
                        $blockedTx = Transaksi::whereIn('parking_map_slot_id', $slotIds)
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
                    }

                    $blockedRsv = ParkingSlotReservation::active()
                        ->whereIn('parking_map_slot_id', $slotIds)
                        ->pluck('parking_map_slot_id')
                        ->all();

                    $blocked = array_flip(array_unique(array_merge($blockedTx, $blockedRsv)));

                    $picked = $candidateSlots->first(function ($s) use ($blocked) {
                        return !isset($blocked[$s->id]);
                    });

                    if (!$picked) {
                        throw new \Exception('Tidak ada slot tersedia di area ini.');
                    }

                    $slotId = $picked->id;
                    $slotCode = $slotCode ?: $picked->code;
                }

                $existsOnSlot = ParkingSlotReservation::active()
                    ->where('parking_map_slot_id', $slotId)
                    ->lockForUpdate()
                    ->exists();
                if ($existsOnSlot) {
                    throw new \Exception('Slot ini sudah dibooking.');
                }

                $occupied = Transaksi::where('parking_map_slot_id', $slotId)
                    ->where(function ($q) use ($tenMinutesAgo) {
                        $q->where(function ($q2) {
                            $q2->whereNull('waktu_keluar')->where('status', 'masuk');
                        })->orWhere(function ($q2) use ($tenMinutesAgo) {
                            $q2->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
                        });
                    })
                    ->exists();
                if ($occupied) {
                    throw new \Exception('Slot ini sedang terisi.');
                }

                return ParkingSlotReservation::create([
                    'parking_map_slot_id' => $slotId,
                    'id_area' => $area->id_area,
                    'id_user' => $userId,
                    'id_kendaraan' => $kendaraan->id_kendaraan,
                    'id_tarif' => $tarifId,
                    'reserved_at' => $reservedAt,
                    'expires_at' => $expiresAt,
                ]);
            });

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Booking berhasil dibuat.', 'reservation' => $reservation], 200);
            }

            return back()->with('success', 'Booking berhasil dibuat.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 409);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function unbookmark(Request $request, $id_transaksi)
    {
        $reservation = ParkingSlotReservation::where('id', $id_transaksi)
            ->where('id_user', Auth::id())
            ->first();

        if ($reservation) {
            $reservation->delete();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Booking berhasil dibatalkan.'], 200);
            }
            return back()->with('success', 'Booking berhasil dibatalkan.');
        }

        $transaksi = Transaksi::where('id_parkir', $id_transaksi)
            ->where('status', 'bookmarked')
            ->where('id_user', Auth::id())
            ->first();

        if (!$transaksi) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bookmark tidak ditemukan atau Anda tidak memiliki izin untuk membatalkannya.'], 404);
            }
            return back()->with('error', 'Bookmark tidak ditemukan atau Anda tidak memiliki izin untuk membatalkannya.');
        }

        $transaksi->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Bookmark berhasil dibatalkan.'], 200);
        }
        return back()->with('success', 'Bookmark berhasil dibatalkan.');
    }

    // Method to display the SVG parking map view
    public function showMap()
    {
        return view('parkir.index');
    }
}
