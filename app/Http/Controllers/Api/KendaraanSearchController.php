<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Support\PlatNomorNormalizer;

class KendaraanSearchController extends Controller
{
    /**
     * Cari kendaraan berdasarkan plat nomor (untuk autocomplete)
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->query('q', '');
        $q = trim($q);

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $kendaraans = Kendaraan::where('plat_nomor', 'like', '%' . $q . '%')
            ->orderBy('plat_nomor')
            ->limit(10)
            ->get(['id_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'warna', 'pemilik']);

        return response()->json($kendaraans);
    }

    /**
     * Cek apakah plat nomor sudah terdaftar (match setelah normalisasi: uppercase, hapus spasi)
     */
    public function checkPlat(Request $request): JsonResponse
    {
        $plat = trim($request->query('plat', ''));

        if (strlen($plat) < 2) {
            return response()->json(['found' => false, 'kendaraan' => null]);
        }

        $platNormalized = PlatNomorNormalizer::normalize($plat);
        $kendaraan = Kendaraan::query()
            ->orderBy('id_kendaraan', 'desc')
            ->get(['id_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'warna', 'pemilik'])
            ->first(function ($item) use ($platNormalized) {
                return PlatNomorNormalizer::normalize((string) $item->plat_nomor) === $platNormalized;
            });

        return response()->json([
            'found' => $kendaraan !== null,
            'kendaraan' => $kendaraan ? [
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'plat_nomor' => $kendaraan->plat_nomor,
                'jenis_kendaraan' => $kendaraan->jenis_kendaraan,
                'warna' => $kendaraan->warna,
                'pemilik' => $kendaraan->pemilik,
            ] : null,
        ]);
    }

    private function normalizePlatNomor(string $plat): string
    {
        return PlatNomorNormalizer::normalize($plat);
    }
}

