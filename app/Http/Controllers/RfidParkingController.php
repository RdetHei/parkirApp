<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\Transaksi;
use App\Models\AreaParkir;
use App\Models\Pembayaran;
use App\Models\RfidTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RfidParkingController extends Controller
{
    public function index()
    {
        $title = 'Parking Scan RFID';
        $operationalArea = null;
        $sessionId = session(PetugasDashboardController::SESSION_OPERATIONAL_AREA);
        if ($sessionId) {
            $operationalArea = AreaParkir::find($sessionId);
        }

        return view('parkir.scan', compact('title', 'operationalArea'));
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
        ]);

        $uid = trim($request->rfid_uid);
        $user = null;
        $kendaraan = null;

        // Coba cari di RfidTag dulu (metode baru, lebih spesifik ke kendaraan)
        $rfidTag = \App\Models\RfidTag::with('kendaraan.user')->where('uid', $uid)->first();

        if ($rfidTag && $rfidTag->kendaraan) {
            $kendaraan = $rfidTag->kendaraan;
            $user = $kendaraan->user;

            if ($rfidTag->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu RFID (' . $uid . ') tidak aktif!',
                    'user' => [
                        'name' => $user?->name ?? 'Unknown',
                        'status' => 'Kartu Inaktif',
                    ]
                ], 403);
            }
        } else {
            // Fallback ke metode lama: cari di tb_user.rfid_uid
            $user = User::where('rfid_uid', $uid)->first();
            if ($user) {
                // Kendaraan default user dipakai saat check-in baru.
                $kendaraan = Kendaraan::where('id_user', $user->id)->first();
            }
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu (' . $uid . ') tidak terdaftar!'
            ], 404);
        }

        // Rate limit per UID untuk menghindari scan dobel super cepat.
        $scanCooldownSeconds = 2;
        $scanKey = 'rfid:scan:last:' . sha1($uid);
        $lastScanAt = Cache::get($scanKey);
        if ($lastScanAt && now()->diffInSeconds(Carbon::parse($lastScanAt)) < $scanCooldownSeconds) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu baru saja discan. Mohon tunggu sebentar sebelum scan ulang.',
                'user' => [
                    'name' => $user->name,
                    'photo' => $user->profile_photo_url,
                    'balance' => $user->balance ?? 0,
                    'status' => 'Scan Terlalu Cepat',
                ],
            ], 429);
        }
        Cache::put($scanKey, now()->toDateTimeString(), now()->addSeconds($scanCooldownSeconds));

        // Cek apakah sedang parkir (transaksi dengan status 'masuk')
        // Gunakan kendaraan_id jika ada (dari RfidTag) untuk akurasi lebih tinggi,
        // fallback ke user_id jika dari tb_user.
        $activeTransaksi = Transaksi::where('id_user', $user->id)
            ->where('status', 'masuk')
            ->when($kendaraan, function($q) use ($kendaraan) {
                return $q->where('id_kendaraan', $kendaraan->id_kendaraan);
            })
            ->latest('waktu_masuk')
            ->first();

        if (!$activeTransaksi) {
            // Scan pertama: Check-in (wajib ada kendaraan terdaftar).
            if (!$kendaraan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in gagal: user belum memiliki kendaraan terdaftar.',
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $user->balance ?? 0,
                        'status' => 'Data Kendaraan Kosong'
                    ]
                ], 400);
            }

            $currentUser = Auth::user();
            $area = $this->resolveCheckInArea($currentUser);

            if (! $area) {
                $msg = $currentUser && $currentUser->role === 'petugas'
                    ? 'Gagal check-in: masukkan Kode Peta di dashboard atau di halaman terminal RFID terlebih dahulu agar auto slot berjalan.'
                    : 'Gagal check-in: atur area dengan Kode Peta di terminal, atau hubungi admin untuk menugaskan area pada akun Anda.';

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $user->balance ?? 0,
                        'status' => 'Belum Ada Area Terminal',
                        'vehicle' => $kendaraan->plat_nomor,
                    ],
                    'requires_map_code' => true,
                ], 403);
            }

            $tarif = Tarif::where('jenis_kendaraan', $kendaraan->jenis_kendaraan)->first() ?? Tarif::first();

            if (!$tarif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal check-in: Tarif untuk jenis kendaraan ini (' . $kendaraan->jenis_kendaraan . ') tidak ditemukan. Silakan hubungi admin.',
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $user->balance ?? 0,
                        'status' => 'Tarif Tidak Ditemukan',
                        'vehicle' => $kendaraan->plat_nomor,
                    ]
                ], 400);
            }

            // Gunakan Database Transaction untuk menjaga integritas data saat assign slot.
            $transaksi = DB::transaction(function () use ($user, $kendaraan, $area, $tarif) {
                // Cari slot tersedia secara otomatis
                $slot = $area->findNextAvailableSlot();

                if (!$slot) {
                    throw new \Exception("Area " . $area->nama_area . " sudah penuh! Tidak ada slot tersedia.");
                }

                $tx = Transaksi::create([
                    'id_user' => $user->id,
                    'id_kendaraan' => $kendaraan->id_kendaraan,
                    'id_area' => $area->id_area,
                    'id_tarif' => $tarif->id_tarif,
                    'parking_map_slot_id' => $slot->id, // Assign slot otomatis
                    'waktu_masuk' => now(),
                    'status' => 'masuk',
                    'status_pembayaran' => 'pending',
                ]);

                $area->increment('terisi');
                return $tx;
            });

            RfidTransaction::create([
                'user_id' => $user->id,
                'type' => 'IN',
                'amount' => null,
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in Berhasil',
                'user' => [
                    'name' => $user->name,
                    'photo' => $user->profile_photo_url,
                    'balance' => $user->balance ?? 0,
                    'status' => 'Parkir Masuk',
                    'vehicle' => $kendaraan->plat_nomor . ' (' . $kendaraan->jenis_kendaraan . ')',
                    'type' => 'IN'
                ]
            ]);
        } else {
            // Scan berikutnya: jika user sudah punya status "masuk", maka check-out.
            $user->refresh();
            $tarif = $activeTransaksi->tarif ?? Tarif::first();
            $rate = $tarif ? $tarif->tarif_perjam : 2000;
            $activeKendaraan = $activeTransaksi->kendaraan ?? $kendaraan;

            $startTime = Carbon::parse($activeTransaksi->waktu_masuk);
            $endTime = \Illuminate\Support\Carbon::now();
            $durationHours = (int) max(1, ceil($startTime->diffInMinutes($endTime, true) / 60));
            $baseAmount = $durationHours * $rate;
            $diskon = 0;

            // Diskon 10% jika user memiliki rfid_uid (kartu member)
            if (!empty($user->rfid_uid)) {
                $diskon = $baseAmount * 0.1;
                $totalAmount = $baseAmount - $diskon;
            } else {
                $totalAmount = $baseAmount;
            }

            $checkout = DB::transaction(function () use ($user, $activeTransaksi, $rate, $totalAmount, $diskon) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                $bal = (float) ($lockedUser->balance ?? 0);

                // Kunci baris transaksi agar scan dobel tidak memproses ulang.
                $lockedTransaksi = Transaksi::query()
                    ->where('id_parkir', $activeTransaksi->id_parkir)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedTransaksi) {
                    return ['mode' => 'error', 'message' => 'Transaksi tidak ditemukan.'];
                }

                // Kalau ternyata transaksi sudah diproses (status sudah bukan "masuk"),
                // jangan bikin transaksi legacy dobel.
                if ($lockedTransaksi->status !== 'masuk') {
                    $amountFromDb = (float) ($lockedTransaksi->biaya_total ?? $totalAmount);
                    if ($lockedTransaksi->status_pembayaran === 'pending') {
                        return ['mode' => 'pending', 'balance' => $bal, 'amount' => $amountFromDb];
                    }
                    return [
                        'mode' => 'paid',
                        'balance' => (float) ($lockedUser->balance ?? 0),
                        'amount' => $amountFromDb,
                    ];
                }

                // Hitung ulang durasi/biaya berdasarkan waktu masuk transaksi yang terkunci.
                $startTime2 = Carbon::parse($lockedTransaksi->waktu_masuk);
                $durationHours2 = (int) max(1, ceil($startTime2->diffInMinutes(\Illuminate\Support\Carbon::now(), true) / 60));
                $baseAmount2 = $durationHours2 * $rate;
                $diskon2 = 0;

                // Diskon 10% jika user memiliki rfid_uid (kartu member)
                if (!empty($lockedUser->rfid_uid)) {
                    $diskon2 = $baseAmount2 * 0.1;
                    $totalAmount2 = $baseAmount2 - $diskon2;
                } else {
                    $totalAmount2 = $baseAmount2;
                }

                if ($bal < $totalAmount2) {
                    $lockedTransaksi->update([
                        'waktu_keluar' => now(),
                        'durasi_jam' => $durationHours2,
                        'biaya_total' => $totalAmount2,
                        'diskon' => $diskon2,
                        'status' => 'keluar',
                        'status_pembayaran' => 'pending',
                    ]);

                    return ['mode' => 'pending', 'balance' => $bal, 'amount' => (float) $totalAmount2];
                }

                $newBal = round($bal - $totalAmount2, 2);
                $lockedUser->update([
                    'balance' => $newBal,
                ]);

                $lockedTransaksi->update([
                    'waktu_keluar' => now(),
                    'durasi_jam' => $durationHours2,
                    'biaya_total' => $totalAmount2,
                    'diskon' => $diskon2,
                    'status' => 'keluar',
                    'status_pembayaran' => 'berhasil',
                ]);

                // Penting untuk akumulasi pendapatan dashboard/report:
                // setiap pembayaran sukses via scan otomatis harus tercatat di tabel pembayaran.
                $pembayaran = Pembayaran::create([
                    'id_parkir' => $lockedTransaksi->id_parkir,
                    'nominal' => $totalAmount2,
                    'metode' => 'nestonpay',
                    'status' => 'berhasil',
                    'id_user' => Auth::id(),
                    'waktu_pembayaran' => now(),
                ]);

                $lockedTransaksi->update([
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                ]);

                return ['mode' => 'paid', 'balance' => (float) $newBal, 'amount' => (float) $totalAmount2];
            });

            $checkoutMode = $checkout['mode'] ?? null;
            $checkoutAmount = (float) ($checkout['amount'] ?? $totalAmount);

            if ($checkoutMode === 'error') {
                return response()->json([
                    'success' => false,
                    'message' => $checkout['message'] ?? 'Sistem bermasalah (Transaksi tidak valid).',
                ], 500);
            }

            if ($checkoutMode === 'pending') {
                RfidTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'OUT_PENDING',
                    'amount' => $checkoutAmount,
                    'created_at' => now(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak cukup! Check-out selesai, silakan bayar Rp ' . number_format($checkoutAmount, 0, ',', '.'),
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $checkout['balance'],
                        'status' => 'Menunggu Pembayaran',
                        'vehicle' => $activeKendaraan->plat_nomor ?? '-',
                        'type' => 'OUT',
                    ],
                    'amount' => $checkoutAmount,
                    'payment_required' => [
                        'id_parkir' => (int) $activeTransaksi->id_parkir,
                        'amount' => $checkoutAmount,
                        'methods' => [
                            'nestonpay' => true,
                            'midtrans' => true,
                        ],
                    ],
                ], 402);
            }

            RfidTransaction::create([
                'user_id' => $user->id,
                'type' => 'OUT',
                'amount' => $checkoutAmount,
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out Berhasil',
                'user' => [
                    'name' => $user->name,
                    'photo' => $user->profile_photo_url,
                    'balance' => $checkout['balance'],
                    'status' => 'Parkir Keluar',
                    'vehicle' => $activeKendaraan->plat_nomor ?? '-',
                    'type' => 'OUT',
                ],
                'amount' => $checkoutAmount,
            ]);
        }
    }

    public function history()
    {
        $items = RfidTransaction::with('user')
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('rfid.history', [
            'title' => 'Riwayat Scan RFID',
            'items' => $items,
        ]);
    }

    public function destroyHistory(int $id)
    {
        $item = RfidTransaction::query()->findOrFail($id);
        $item->delete();

        return back()->with('success', 'Riwayat RFID berhasil dihapus.');
    }

    /**
     * Area check-in: sesi kode peta (wajib untuk petugas); admin bisa fallback id_area.
     */
    private function resolveCheckInArea(?User $currentUser): ?AreaParkir
    {
        if (! $currentUser) {
            return null;
        }

        // Prioritas: Area yang ditugaskan ke user (petugas/admin)
        if ($currentUser->id_area) {
            return AreaParkir::find($currentUser->id_area);
        }

        // Fallback: Sesi (jika masih ada, tapi prefer id_area)
        $sessionId = session(PetugasDashboardController::SESSION_OPERATIONAL_AREA);
        if ($sessionId) {
            return AreaParkir::find($sessionId);
        }

        return null;
    }
}

