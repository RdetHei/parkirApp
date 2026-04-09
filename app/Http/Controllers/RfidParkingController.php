<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
        return view('parkir.scan', compact('title'));
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
        ]);

        $uid = trim($request->rfid_uid);
        $user = User::where('rfid_uid', $uid)
                    ->orWhere('nfc_uid', $uid)
                    ->first();

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
                    'balance' => $user->balance ?? $user->saldo ?? 0,
                    'status' => 'Scan Terlalu Cepat',
                ],
            ], 429);
        }
        Cache::put($scanKey, now()->toDateTimeString(), now()->addSeconds($scanCooldownSeconds));

        // Cek apakah sedang parkir (transaksi dengan status 'masuk')
        $activeTransaksi = Transaksi::where('id_user', $user->id)
            ->where('status', 'masuk')
            ->latest('waktu_masuk')
            ->first();

        // Kendaraan default user dipakai saat check-in baru.
        $kendaraan = Kendaraan::where('id_user', $user->id)->first();

        if (!$activeTransaksi) {
            // Scan pertama: Check-in (wajib ada kendaraan terdaftar).
            if (!$kendaraan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in gagal: user belum memiliki kendaraan terdaftar.',
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $user->balance ?? $user->saldo ?? 0,
                        'status' => 'Data Kendaraan Kosong'
                    ]
                ], 400);
            }

            $area = AreaParkir::where('is_default_map', true)->first() ?? AreaParkir::first();
            $tarif = Tarif::where('jenis_kendaraan', $kendaraan->jenis_kendaraan)->first() ?? Tarif::first();

            if (!$area) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal check-in: Area parkir tidak tersedia.',
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $user->balance ?? $user->saldo ?? 0,
                        'status' => 'Area Tidak Tersedia',
                        'vehicle' => $kendaraan->plat_nomor,
                    ]
                ], 400);
            }

            if (!$tarif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal check-in: Tarif untuk jenis kendaraan ini (' . $kendaraan->jenis_kendaraan . ') tidak ditemukan. Silakan hubungi admin.',
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $user->balance ?? $user->saldo ?? 0,
                        'status' => 'Tarif Tidak Ditemukan',
                        'vehicle' => $kendaraan->plat_nomor,
                    ]
                ], 400);
            }

            $transaksi = Transaksi::create([
                'id_user' => $user->id,
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'id_area' => $area->id_area,
                'id_tarif' => $tarif->id_tarif,
                'waktu_masuk' => now(),
                'status' => 'masuk',
                'status_pembayaran' => 'pending',
            ]);

            // Create legacy Transaction for backward compatibility if needed
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'IN',
                'amount' => null,
                'created_at' => now(),
            ]);
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
                    'balance' => $user->balance ?? $user->saldo ?? 0,
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
            $endTime = now();
            $durationHours = max(1, ceil($startTime->diffInMinutes($endTime) / 60));
            $totalAmount = $durationHours * $rate;

            $checkout = DB::transaction(function () use ($user, $activeTransaksi, $rate, $totalAmount) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                $bal = (float) ($lockedUser->balance ?? $lockedUser->saldo ?? 0);

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
                        'balance' => (float) ($lockedUser->balance ?? $lockedUser->saldo ?? 0),
                        'amount' => $amountFromDb,
                    ];
                }

                // Hitung ulang durasi/biaya berdasarkan waktu masuk transaksi yang terkunci.
                $startTime2 = Carbon::parse($lockedTransaksi->waktu_masuk);
                $durationHours2 = max(1, ceil($startTime2->diffInMinutes(now()) / 60));
                $totalAmount2 = $durationHours2 * $rate;

                if ($bal < $totalAmount2) {
                    $lockedTransaksi->update([
                        'waktu_keluar' => now(),
                        'durasi_jam' => $durationHours2,
                        'biaya_total' => $totalAmount2,
                        'status' => 'keluar',
                        'status_pembayaran' => 'pending',
                    ]);

                    Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'OUT',
                        'amount' => $totalAmount2,
                        'created_at' => now(),
                    ]);

                    return ['mode' => 'pending', 'balance' => $bal, 'amount' => (float) $totalAmount2];
                }

                $newBal = round($bal - $totalAmount2, 2);
                $lockedUser->update([
                    'balance' => $newBal,
                    'saldo' => $newBal,
                ]);

                $lockedTransaksi->update([
                    'waktu_keluar' => now(),
                    'durasi_jam' => $durationHours2,
                    'biaya_total' => $totalAmount2,
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

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'OUT',
                    'amount' => $totalAmount2,
                    'created_at' => now(),
                ]);

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'PAYMENT',
                    'amount' => $totalAmount2,
                    'created_at' => now(),
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
}

