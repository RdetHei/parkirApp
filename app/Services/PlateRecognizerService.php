<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class PlateRecognizerService
{
    private string $apiUrl;
    private string $apiKey;
    private float $confidenceThreshold;

    public function __construct()
    {
        $this->apiUrl = 'https://api.platerecognizer.com/v1/plate-reader/';
        $this->apiKey = config('services.plate_recognizer.key', env('PLATE_RECOGNIZER_KEY', ''));
        $this->confidenceThreshold = 0.80; // 80%
    }

    /**
     * Memindai plat nomor dari gambar yang diunggah
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param bool $includeRawResponse
     * @return array
     * @throws \Exception
     */
    public function scanPlate($image, bool $includeRawResponse = false): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Kunci API Deteksi Plat tidak dikonfigurasi. Pastikan PLATE_RECOGNIZER_KEY ada di file .env');
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Token ' . $this->apiKey,
                ])
                ->attach('upload', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
                ->post($this->apiUrl);

            // Periksa apakah permintaan gagal
            if ($response->failed()) {
                $errorMessage = $response->json('detail') ?? $response->json('message') ?? 'Permintaan API gagal';
                $statusCode = $response->status();
                
                Log::error('Kesalahan API Deteksi Plat', [
                    'status' => $statusCode,
                    'response' => $response->body(),
                ]);

                throw new \Exception("Kesalahan API Deteksi Plat: {$errorMessage} (Status: {$statusCode})");
            }

            $data = $response->json();

            // Periksa apakah respons kosong atau tidak ada hasil
            if (empty($data) || !isset($data['results']) || count($data['results']) === 0) {
                return [
                    'plate_number' => null,
                    'confidence' => 0,
                    'valid' => false,
                    'message' => 'Tidak ada plat nomor yang terdeteksi dalam gambar',
                    'raw_response' => $includeRawResponse ? $data : null,
                ];
            }

            // Ambil hasil pertama (dengan tingkat akurasi tertinggi)
            $firstResult = $data['results'][0];
            $plateNumber = $firstResult['plate'] ?? null;
            $confidence = floatval($firstResult['score'] ?? 0);

            // Ekstrak warna jika tersedia
            $color = $firstResult['vehicle']['color'][0]['label'] ?? ($firstResult['color'][0]['label'] ?? null);

            // Periksa ambang batas tingkat akurasi (confidence threshold)
            $isValid = $confidence >= $this->confidenceThreshold;

            return [
                'plate_number' => $plateNumber,
                'color' => $color,
                'confidence' => $confidence,
                'valid' => $isValid,
                'message' => $isValid 
                    ? 'Plat nomor berhasil dideteksi' 
                    : 'Plat tidak valid (tingkat akurasi di bawah 80%)',
                'raw_response' => $includeRawResponse ? $data : null,
            ];

        } catch (RequestException $e) {
            Log::error('Plate Recognizer HTTP Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Gagal terhubung ke Plate Recognizer API: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Plate Recognizer Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Get confidence threshold
     *
     * @return float
     */
    public function getConfidenceThreshold(): float
    {
        return $this->confidenceThreshold;
    }
}

