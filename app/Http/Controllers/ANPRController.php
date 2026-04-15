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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Traits\LogsActivity;
use App\Support\PlatNomorNormalizer;
use App\Services\PlateRecognizerService;

class ANPRController extends Controller
{
    use LogsActivity;

    private PlateRecognizerService $plateRecognizerService;

    public function __construct(PlateRecognizerService $plateRecognizerService)
    {
        $this->plateRecognizerService = $plateRecognizerService;
    }

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

            // 1. Verifikasi ambang batas tingkat akurasi (confidence threshold)
            if ($confidence < 0.8) {
                return response()->json(['error' => 'Tingkat akurasi deteksi terlalu rendah'], 422);
            }

            // 2. Pengunggahan dan pengelolaan gambar ke Cloudinary
            $fileName = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'neston/anpr'
                ]);
                $fileName = $upload['secure_url'] ?? null;
            }

            // 3. Logika Pencatatan Kendaraan & Transaksi Parkir
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
                // Perbarui detail kendaraan jika data masih bersifat umum (generic)
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
                // LOGIKA MASUK (ENTRY)
                $statusAction = 'entry';

                $currentUser = Auth::user();
                $area = null;

                if ($currentUser && $currentUser->role === 'petugas') {
                    $sessionArea = session(PetugasDashboardController::SESSION_OPERATIONAL_AREA);
                    if ($sessionArea) {
                        $area = AreaParkir::find($sessionArea);
                    }
                } elseif ($currentUser && $currentUser->id_area) {
                    $area = AreaParkir::find($currentUser->id_area);
                }

                // Fallback jika petugas tidak memiliki area atau tidak sedang login (scan melalui terminal mandiri)
                if (!$area) {
                    $area = AreaParkir::where('is_default_map', true)->first() ?? AreaParkir::first();
                }

                $defaultTarif = Tarif::where('jenis_kendaraan', $vehicleType)->first() ?? Tarif::first();
                $adminUser = User::where('role', 'admin')->first() ?? User::first();

                // Gunakan Transaksi Database untuk menjaga integritas data saat penentuan slot parkir.
                $transaksi = DB::transaction(function () use ($kendaraan, $area, $defaultTarif, $adminUser) {
                    // Cari slot yang tersedia secara otomatis di area tersebut
                    $slot = $area->findNextAvailableSlot();

                    if (!$slot) {
                        // Jika area parkir penuh, transaksi tetap dicatat namun muncul peringatan di log sistem
                        Log::warning("Area " . $area->nama_area . " penuh saat deteksi otomatis untuk plat: " . $kendaraan->plat_nomor);
                    }

                    $tx = Transaksi::create([
                        'id_kendaraan' => $kendaraan->id_kendaraan,
                        'waktu_masuk' => now(),
                        'id_tarif' => $defaultTarif->id_tarif,
                        'id_area' => $area->id_area,
                        'parking_map_slot_id' => $slot ? $slot->id : null, // Penugasan slot otomatis jika tersedia
                        'status' => 'masuk',
                        'id_user' => $adminUser->id,
                        'status_pembayaran' => 'pending',
                    ]);

                    $area->increment('terisi');
                    return $tx;
                });

                $this->logActivity(
                    "Deteksi Otomatis (Masuk): Kendaraan {$plateNumber} masuk",
                    'transaksi',
                    $transaksi,
                    ['plate' => $plateNumber, 'confidence' => $confidence]
                );
            } else {
                // LOGIKA KELUAR (EXIT) - Mencegah proses ganda dan menjaga konsistensi data
                $statusAction = 'exit';
                $this->applyCheckoutTotals($activeTransaksi, Carbon::now());
                $activeTransaksi->area->decrement('terisi');
                $transaksi = $activeTransaksi->fresh();

                $this->logActivity(
                    "Deteksi Otomatis (Keluar): Kendaraan {$plateNumber} keluar",
                    'transaksi',
                    $transaksi,
                    ['plate' => $plateNumber, 'confidence' => $confidence]
                );
            }

            // 4. Pencatatan Log Riwayat Deteksi
            $rawJson = $request->raw_json;
            if (is_string($rawJson)) {
                $rawJson = json_decode($rawJson, true);
            }

            ANPRScan::create([
                'plat_nomor' => $plateNumber,
                'confidence' => $confidence,
                'image_path' => $fileName,
                'scan_time' => now(),
                'json_response' => $rawJson,
                'id_parkir' => $transaksi->id_parkir,
            ]);

            // 5. Buat Notifikasi untuk pembaruan UI secara real-time (melalui polling)
            \App\Models\NotificationLog::create([
                'user_id' => $kendaraan->id_user,
                'type' => 'anpr_detection',
                'message' => "Kendaraan {$plateNumber} terdeteksi " . ($statusAction === 'entry' ? 'masuk' : 'keluar'),
                'data' => json_encode([
                    'plate' => $plateNumber,
                    'action' => $statusAction,
                    'confidence' => $confidence,
                    'image_url' => $fileName,
                    'vehicle' => [
                        'color' => $vehicleColor,
                        'type' => $vehicleType,
                        'display_name' => $vehicleDisplayName
                    ]
                ]),
                'status' => 'success'
            ]);

            // 6. Jalankan Event (untuk fitur real-time broadcasting jika diaktifkan)
            event(new \App\Events\ANPRDetected([
                'plate' => $plateNumber,
                'action' => $statusAction,
                'confidence' => $confidence,
                'image_url' => $fileName,
                'vehicle' => [
                    'color' => $vehicleColor,
                    'type' => $vehicleType,
                    'display_name' => $vehicleDisplayName
                ]
            ]));

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
            Log::error('Kesalahan Sistem Deteksi Otomatis: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server'], 500);
        }
    }

    public function scan(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            // 1. Upload to Cloudinary
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'neston/anpr'
                ]);
                $imageUrl = $upload['secure_url'] ?? null;
            }

            if (!$imageUrl) {
                return response()->json(['error' => 'Gagal mengunggah gambar'], 500);
            }

            // 2. Use the existing PlateRecognizerService
            $scanResult = $this->plateRecognizerService->scanPlate($request->file('image'), true);

            if (!$scanResult['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $scanResult['message'] ?? 'Gagal mendeteksi plat nomor',
                    'image_url' => $imageUrl
                ]);
            }

            $plateNumber = PlatNomorNormalizer::normalize($scanResult['plate_number']);
            $confidence = $scanResult['confidence'];

            // Extract MMC (Make, Model, Color) from raw response if available
            $raw = $scanResult['raw_response'];
            $vehicleColor = data_get($raw, 'results.0.vehicle.color.0.color', 'unknown');
            $vehicleType = data_get($raw, 'results.0.vehicle.type.0.type', 'mobil');
            $boundingBox = data_get($raw, 'results.0.box');

            // 3. Match with database
            $kendaraan = Kendaraan::where('plat_nomor', $plateNumber)->with('user')->first();
            $transaksi = null;

            if ($kendaraan) {
                $transaksi = Transaksi::where('id_kendaraan', $kendaraan->id_kendaraan)
                    ->where('status', 'masuk')
                    ->latest()
                    ->first();
            }

            return response()->json([
                'success' => true,
                'plate_number' => $plateNumber,
                'confidence' => $confidence,
                'vehicle' => [
                    'color' => $vehicleColor,
                    'type' => $vehicleType,
                    'is_registered' => !!$kendaraan,
                    'owner' => $kendaraan?->user?->name
                ],
                'box' => $boundingBox,
                'transaksi' => $transaksi,
                'image_url' => $imageUrl
            ]);

        } catch (\Exception $e) {
            Log::error('ANPR Scan Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function applyCheckoutTotals(Transaksi $transaksi, Carbon $waktuKeluar): void
    {
        $transaksi->loadMissing('tarif');
        $tarif = $transaksi->tarif;
        if (! $tarif) {
            throw new \RuntimeException('Tarif tidak ditemukan untuk transaksi ANPR.');
        }

        $durasi_menit = abs($waktuKeluar->diffInMinutes($transaksi->waktu_masuk));
        $durasi_jam = (int) ceil($durasi_menit / 60);

        if ($durasi_jam < 1) {
            $durasi_jam = 1;
        }

        $biaya_total = $durasi_jam * $tarif->tarif_perjam;

        $transaksi->update([
            'waktu_keluar' => $waktuKeluar,
            'durasi_jam' => $durasi_jam,
            'biaya_total' => $biaya_total,
            'status' => 'keluar',
        ]);
    }
}
