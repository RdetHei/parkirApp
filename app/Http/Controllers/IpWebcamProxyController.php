<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class IpWebcamProxyController extends Controller
{
    /**
     * Fetch a single snapshot from IP Webcam and return it same-origin.
     *
     * Reason: drawing a cross-origin <img> to canvas taints it, so canvas export fails.
     */
    public function snapshot(Request $request)
    {
        $request->validate([
            'url' => 'required|string|max:2048',
        ]);

        $url = trim((string) $request->input('url'));

        $parts = @parse_url($url);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            throw ValidationException::withMessages(['url' => ['URL tidak valid.']]);
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        $port = (int) ($parts['port'] ?? 80);

        if (!in_array($scheme, ['http', 'https'], true)) {
            throw ValidationException::withMessages(['url' => ['Scheme URL harus http/https.']]);
        }

        // Expanded whitelist: localhost AND private network IPs (LAN)
        $isLocal = ($host === 'localhost' || $host === '127.0.0.1');
        $isPrivateIp = false;

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            // Check if IP is in private ranges: 10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16
            $isPrivateIp = !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        if (!$isLocal && !$isPrivateIp) {
            throw ValidationException::withMessages(['url' => ['Host tidak diizinkan. Gunakan IP lokal / LAN.']]);
        }

        // Normalize common IP Webcam stream URL to snapshot URL
        // e.g. http://192.168.1.5:8080/video -> http://192.168.1.5:8080/shot.jpg
        if (str_ends_with($url, '/video')) {
            $url = substr($url, 0, -strlen('/video')) . '/shot.jpg';
        }

        // If user gave base URL without path (e.g. http://192.168.1.5:8080), append /shot.jpg
        $path = $parts['path'] ?? '';
        if ($path === '' || $path === '/') {
            $url = rtrim($url, '/') . '/shot.jpg';
        }

        $response = Http::timeout(5)
            ->withOptions(['verify' => false])
            ->get($url);

        if (!$response->ok()) {
            return response()->json([
                'message' => 'Gagal mengambil snapshot dari IP Webcam.',
                'status' => $response->status(),
            ], 502);
        }

        $contentType = $response->header('Content-Type') ?: 'image/jpeg';

        return response($response->body(), 200)
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}

