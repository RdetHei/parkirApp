<?php

namespace App\Http\Controllers;

use App\Models\ANPRScan;
use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Traits\LogsActivity;
use App\Support\PlatNomorNormalizer;

class ANPRController extends Controller
{
    use LogsActivity;

    public function handleDetection(Request $request)
    {
        $request->validate([
            'plate' => 'required|string',
            'confidence' => 'required|numeric',
            'vehicle_type' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'vehicle_make' => 'nullable|string',
            'vehicle_model' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $plateNumber = PlatNomorNormalizer::normalize($request->plate);
            $confidence = $request->confidence;

            // 1. Threshold check
            if ($confidence < 0.8) {
                return response()->json(['error' => 'Confidence too low'], 422);
            }

            // 2. Image handling
            $fileName = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'neston/anpr'
                ]);
                $fileName = $upload['secure_url'];
            }

            // 3. Vehicle & Transaction Logic
            $vehicleDisplayName = $request->vehicle_make ? ($request->vehicle_make . ' ' . $request->vehicle_model) : 'Guest';
            $vehicleType = $request->vehicle_type ?? 'mobil';
            $vehicleColor = $request->vehicle_color ?? 'unknown';

            $kendaraan = Kendaraan::where('plat_nomor', $plateNumber)->first();
            if (!$kendaraan) {
                $adminUser = User::where('role', 'admin')->first() ?? User::first();
                $kendaraan = Kendaraan::create([
                    'plat_nomor' => $plateNumber,
                    'jenis_kendaraan' => $vehicleType,
                    'warna' => $vehicleColor,
                    'pemilik' => $vehicleDisplayName,
                    'id_user' => $adminUser->id,
                ]);
            } else {
                // Update vehicle details if generic
                if ($kendaraan->warna === 'unknown' || $kendaraan->pemilik === 'Guest') {
                    $kendaraan->update([
                        'warna' => $vehicleColor,
                        'jenis_kendaraan' => $vehicleType,
                        'pemilik' => $vehicleDisplayName !== 'Guest' ? $vehicleDisplayName : $kendaraan->pemilik,
                    ]);
                }
            }

            $activeTransaksi = Transaksi::where('id_kendaraan', $kendaraan->id_kendaraan)
                ->where('status', 'masuk')
                ->latest()
                ->first();

            $statusAction = '';
            $transaksi = null;

            if (!$activeTransaksi) {
                // ENTRY LOGIC
                $statusAction = 'entry';
                $defaultArea = AreaParkir::first();
                $defaultTarif = Tarif::where('jenis_kendaraan', $vehicleType)->first() ?? Tarif::first();
                $adminUser = User::where('role', 'admin')->first() ?? User::first();

                $transaksi = Transaksi::create([
                    'id_kendaraan' => $kendaraan->id_kendaraan,
                    'waktu_masuk' => now(),
                    'id_tarif' => $defaultTarif->id_tarif,
                    'status' => 'masuk',
                    'id_user' => $adminUser->id,
                    'id_area' => $defaultArea->id_area,
                ]);

                $defaultArea->increment('terisi');

                $this->logActivity(
                    "ANPR Detection (Entry): Kendaraan {$plateNumber} masuk",
                    'transaksi',
                    $transaksi,
                    ['plate' => $plateNumber, 'confidence' => $confidence]
                );
            } else {
                // EXIT LOGIC
                $statusAction = 'exit';
                $activeTransaksi->update([
                    'waktu_keluar' => now(),
                    'status' => 'keluar',
                ]);

                $activeTransaksi->durasi_jam = $activeTransaksi->durasi_jam; // Trigger accessor
                $activeTransaksi->biaya_total = $activeTransaksi->biaya_total; // Trigger accessor
                $activeTransaksi->save();

                $activeTransaksi->area->decrement('terisi');
                $transaksi = $activeTransaksi;

                $this->logActivity(
                    "ANPR Detection (Exit): Kendaraan {$plateNumber} keluar",
                    'transaksi',
                    $transaksi,
                    ['plate' => $plateNumber, 'confidence' => $confidence]
                );
            }

            // 4. Logging
            ANPRScan::create([
                'plat_nomor' => $plateNumber,
                'confidence' => $confidence,
                'image_path' => $fileName,
                'scan_time' => now(),
                'json_response' => $request->raw_json,
                'id_parkir' => $transaksi->id_parkir,
            ]);

            return response()->json([
                'success' => true,
                'action' => $statusAction,
                'plate' => $plateNumber,
                'vehicle' => [
                    'color' => $vehicleColor,
                    'type' => $vehicleType,
                    'display_name' => $vehicleDisplayName
                ],
                'transaksi' => $transaksi
            ]);

        } catch (\Exception $e) {
            Log::error('ANPR YOLO API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }

    public function scan(Request $request)
    {
        $request->validate([
            'image' => 'required|string', // Base64 image from camera or file upload
        ]);

        try {
            // 1. Decode and store image
            $imageData = $request->input('image');
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, etc

                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    return response()->json(['error' => 'Invalid image type'], 422);
                }
                $imageData = base64_decode($imageData);
            } else {
                return response()->json(['error' => 'Invalid image data format'], 422);
            }

            $upload = cloudinary()->uploadApi()->upload("data:image/{$type};base64," . base64_encode($imageData), [
                'folder' => 'neston/anpr'
            ]);
            $imageUrl = $upload['secure_url'];
            $fileName = $imageUrl;

            // 2. Send to Plate Recognizer API with Make/Model/Color detection
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . config('services.plate_recognizer.key'),
            ])->attach(
                'upload', $imageData, basename($fileName)
            )->post('https://api.platerecognizer.com/v1/plate-reader/', [
                'mmc' => 'true' // Enable Make, Model, and Color detection
            ]);

            if ($response->failed()) {
                Log::error('Plate Recognizer API Error: ' . $response->body());
                return response()->json(['error' => 'OCR Service error'], 500);
            }

            $result = $response->json();

            // Handle different JSON structures (Plate Recognizer vs ALPR)
            $plateData = null;
            $plateNumber = null;
            $confidence = 0;
            $vehicleColor = 'unknown';
            $vehicleType = 'mobil';
            $vehicleMake = null;
            $vehicleModel = null;
            $boundingBox = null;

            if (isset($result['results']) && !empty($result['results'])) {
                // Original Plate Recognizer format
                $plateData = $result['results'][0];
                $plateNumber = PlatNomorNormalizer::normalize((string) ($plateData['plate'] ?? ''));
                $confidence = $plateData['score'] ?? $plateData['confidence'] ?? 0;
                $vehicleColor = $plateData['vehicle']['color'][0]['color'] ?? 'unknown';
                $vehicleType = $plateData['vehicle']['type'][0]['type'] ?? 'mobil';
                $vehicleMake = $plateData['vehicle']['make'][0]['make'] ?? null;
                $vehicleModel = $plateData['vehicle']['model'][0]['model'] ?? null;
                $boundingBox = $plateData['box'] ?? null;
            } elseif (is_array($result) && isset($result[0]['plate'])) {
                // New ALPR format from user
                $item = $result[0];
                $plateNumber = PlatNomorNormalizer::normalize((string) ($item['plate']['props']['plate'][0]['value'] ?? ''));
                $confidence = $item['plate']['score'] ?? 0;
                $vehicleColor = $item['vehicle']['props']['color'][0]['value'] ?? 'unknown';
                $vehicleType = $item['vehicle']['type'] ?? 'mobil';
                $vehicleMake = $item['vehicle']['props']['make_model'][0]['make'] ?? null;
                $vehicleModel = $item['vehicle']['props']['make_model'][0]['model'] ?? null;
                $boundingBox = $item['plate']['box'] ?? null;
            }

            if (!$plateNumber) {
                return response()->json(['error' => 'No license plate detected'], 404);
            }

            $vehicleDisplayName = $vehicleMake ? ($vehicleMake . ($vehicleModel ? ' ' . $vehicleModel : '')) : 'Guest';

            // 3. Confidence check (threshold 0.8)
            if ($confidence < 0.8) {
                return response()->json([
                    'error' => 'Confidence too low (' . round($confidence, 2) . '). Please re-scan.',
                    'plate' => $plateNumber,
                    'confidence' => $confidence,
                    'box' => $boundingBox
                ], 422);
            }

            // 4. Vehicle Entry/Exit Logic
            // Find or Create Vehicle
            $kendaraan = Kendaraan::where('plat_nomor', $plateNumber)->first();
            if (!$kendaraan) {
                // If new vehicle, we create it with detected details.
                $adminUser = User::where('role', 'admin')->first() ?? User::first();
                $kendaraan = Kendaraan::create([
                    'plat_nomor' => $plateNumber,
                    'jenis_kendaraan' => $vehicleType,
                    'warna' => $vehicleColor,
                    'pemilik' => $vehicleDisplayName,
                    'id_user' => $adminUser->id,
                ]);
            } else {
                // Update existing vehicle details if unknown
                if ($kendaraan->warna === 'unknown' || $kendaraan->pemilik === 'Guest') {
                    $kendaraan->update([
                        'warna' => $vehicleColor,
                        'jenis_kendaraan' => $vehicleType,
                        'pemilik' => $vehicleDisplayName !== 'Guest' ? $vehicleDisplayName : $kendaraan->pemilik,
                    ]);
                }
            }

            // Check if there is an active transaction for this vehicle
            $activeTransaksi = Transaksi::where('id_kendaraan', $kendaraan->id_kendaraan)
                ->where('status', 'masuk')
                ->latest()
                ->first();

            $statusAction = '';
            $transaksi = null;

            if (!$activeTransaksi) {
                // ENTRY LOGIC
                $statusAction = 'entry';
                $defaultArea = AreaParkir::first();
                $defaultTarif = Tarif::where('jenis_kendaraan', 'mobil')->first() ?? Tarif::first();
                $adminUser = User::where('role', 'admin')->first() ?? User::first();

                $transaksi = Transaksi::create([
                    'id_kendaraan' => $kendaraan->id_kendaraan,
                    'waktu_masuk' => now(),
                    'id_tarif' => $defaultTarif->id_tarif,
                    'status' => 'masuk',
                    'id_user' => $adminUser->id,
                    'id_area' => $defaultArea->id_area,
                ]);

                // Update area occupancy
                $defaultArea->increment('terisi');

                $this->logActivity(
                    "ANPR Scan (Entry): Kendaraan {$plateNumber} masuk",
                    'transaksi',
                    $transaksi,
                    ['plate' => $plateNumber, 'confidence' => $confidence]
                );
            } else {
                // EXIT LOGIC
                $statusAction = 'exit';
                $activeTransaksi->update([
                    'waktu_keluar' => now(),
                    'status' => 'keluar',
                ]);

                // Calculate duration and total cost (using accessors logic if needed, but saving it)
                $activeTransaksi->durasi_jam = $activeTransaksi->durasi_jam; // Trigger accessor
                $activeTransaksi->biaya_total = $activeTransaksi->biaya_total; // Trigger accessor
                $activeTransaksi->save();

                // Update area occupancy
                $activeTransaksi->area->decrement('terisi');
                $transaksi = $activeTransaksi;

                $this->logActivity(
                    "ANPR Scan (Exit): Kendaraan {$plateNumber} keluar",
                    'transaksi',
                    $transaksi,
                    ['plate' => $plateNumber, 'confidence' => $confidence]
                );
            }

            // 5. Save ANPR Scan log
            ANPRScan::create([
                'plat_nomor' => $plateNumber,
                'confidence' => $confidence,
                'image_path' => $fileName,
                'scan_time' => now(),
                'json_response' => $result,
                'id_parkir' => $transaksi->id_parkir,
            ]);

            return response()->json([
                'success' => true,
                'action' => $statusAction,
                'plate' => $plateNumber,
                'confidence' => $confidence,
                'vehicle' => [
                    'color' => $vehicleColor,
                    'type' => $vehicleType,
                    'make' => $vehicleMake,
                    'model' => $vehicleModel,
                    'display_name' => $vehicleDisplayName
                ],
                'box' => $boundingBox,
                'transaksi' => $transaksi,
                'image_url' => asset('storage/' . $fileName)
            ]);

        } catch (\Exception $e) {
            Log::error('ANPR Scan Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
