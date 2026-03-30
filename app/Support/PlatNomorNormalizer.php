<?php

namespace App\Support;

class PlatNomorNormalizer
{
    /**
     * Normalisasi plat nomor untuk konsistensi pencarian & validasi.
     * Rule: trim, uppercase, hapus semua karakter selain A-Z dan 0-9.
     */
    public static function normalize(string $plat): string
    {
        $normalized = preg_replace('/[^A-Z0-9]/i', '', trim($plat));

        return strtoupper($normalized ?? '');
    }

    /**
     * Bandingkan 2 plat nomor dengan normalisasi yang sama.
     */
    public static function equals(string $a, string $b): bool
    {
        return self::normalize($a) === self::normalize($b);
    }
}

