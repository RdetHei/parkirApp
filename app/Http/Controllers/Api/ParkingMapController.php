<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AreaParkir;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Import Auth facade

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
                $bookmarkedBy = $bookmarkedTransaction->user->name ?? 'N/A';
                $bookmarkedAt = $bookmarkedTransaction->bookmarked_at->format('H:i') ?? 'N/A';
                // Add id_transaksi for bookmarked slot to allow unbookmarking/check-in
                $vehicle = [
                    'id_transaksi' => $bookmarkedTransaction->id_parkir,
                    'operator' => $bookmarkedBy, // User who bookmarked
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

        // Check if area is actually empty (no active 'masuk' or unexpired 'bookmarked' transactions)
        $existingActiveTransaction = Transaksi::where('id_area', $area->id_area)
            ->where(function($query) {
                $query->whereNull('waktu_keluar') // Active 'masuk'
                      ->where('status', 'masuk');
            })->orWhere(function($query) {
                $query->where('status', 'bookmarked')
                      ->where('bookmarked_at', '>', Carbon::now()->subMinutes(10)); // Unexpired bookmarked
            })
            ->first();

        if ($existingActiveTransaction) {
            return response()->json(['message' => 'Slot parkir ini sedang terisi atau sudah dibookmark.'], 409);
        }

        // Create a new Transaksi entry for bookmarking
        $transaksi = Transaksi::create([
            'id_kendaraan' => null, // No vehicle yet
            'waktu_masuk' => null,  // No check-in yet
            'waktu_keluar' => null,
            'id_tarif' => null, // No tariff yet
            'durasi_jam' => null,
            'biaya_total' => 0,
            'status' => 'bookmarked',
            'catatan' => 'Slot dibookmark',
            'id_user' => Auth::id(), // User who bookmarked
            'id_area' => $area->id_area,
            'status_pembayaran' => 'pending',
            'id_pembayaran' => null,
            'bookmarked_at' => Carbon::now(),
        ]);
        
        // Optionally increment terisi for area, but only for 'masuk' status.
        // For bookmarked, 'terisi' shouldn't necessarily increment.
        // If 'terisi' should reflect bookmarked too, this logic needs adjustment.
        // For now, it only counts actual parked vehicles.

        return response()->json(['message' => 'Slot berhasil dibookmark.', 'transaksi' => $transaksi], 200);
    }

    public function unbookmark($id_transaksi)
    {
        $transaksi = Transaksi::where('id_parkir', $id_transaksi)
                              ->where('status', 'bookmarked')
                              ->where('id_user', Auth::id()) // Only allow user to unbookmark their own slot
                              ->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Bookmark tidak ditemukan atau Anda tidak memiliki izin untuk membatalkannya.'], 404);
        }

        $transaksi->delete(); // Delete the temporary bookmark transaction

        return response()->json(['message' => 'Bookmark berhasil dibatalkan.'], 200);
    }

    // Method to display the SVG parking map view
    public function showMap()
    {
        return view('parkir.index');
    }
}