<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Tarif;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\Transaksi;
use App\Models\AreaParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Ambil kendaraan default user (yang pertama didaftarkan)
        $kendaraan = Kendaraan::where('id_user', $user->id)->first();

        // --- FITUR BARU: AUTO-PAYMENT TAGIHAN TERTUNDA ---
        // Cek apakah user memiliki tagihan yang belum dibayar (status 'keluar' dan status_pembayaran != 'berhasil')
        $unpaidTransaksi = Transaksi::where('id_user', $user->id)
            ->where('status', 'keluar')
            ->where(function($q) {
                $q->whereNull('status_pembayaran')
                  ->orWhere('status_pembayaran', '!=', 'berhasil');
            })
            ->first();

        if ($unpaidTransaksi && $unpaidTransaksi->biaya_total > 0) {
            $totalTagihan = (float) $unpaidTransaksi->biaya_total;
            $currentSaldo = (float) ($user->balance ?? $user->saldo ?? 0);

            if ($currentSaldo >= $totalTagihan) {
                DB::transaction(function () use ($user, $unpaidTransaksi, $totalTagihan) {
                    // Potong Saldo
                    $user->decrement('balance', $totalTagihan);
                    $user->decrement('saldo', $totalTagihan);

                    // Update Transaksi
                    $unpaidTransaksi->update([
                        'status' => 'keluar',
                        'status_pembayaran' => 'berhasil'
                    ]);

                    // Catat riwayat saldo
                    \App\Models\SaldoHistory::create([
                        'user_id' => $user->id,
                        'amount' => -$totalTagihan,
                        'type' => 'payment',
                        'description' => 'Pelunasan Otomatis RFID - ' . ($unpaidTransaksi->kendaraan?->plat_nomor ?? 'Parkir'),
                        'reference_id' => (string) $unpaidTransaksi->id_parkir,
                    ]);

                    // Legacy Transaction log
                    Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'PAYMENT',
                        'amount' => $totalTagihan,
                        'created_at' => now(),
                    ]);
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Tagihan Rp ' . number_format($totalTagihan, 0, ',', '.') . ' berhasil dilunasi otomatis via Saldo NestonPay!',
                    'amount' => (float) $totalTagihan,
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $currentSaldo - $totalTagihan,
                        'status' => 'Tagihan Dilunasi',
                        'vehicle' => $unpaidTransaksi->kendaraan?->plat_nomor ?? null,
                        'type' => 'OUT',
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda memiliki tagihan Rp ' . number_format($totalTagihan, 0, ',', '.') . ' yang belum dibayar. Saldo tidak mencukupi untuk pelunasan otomatis.',
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $currentSaldo,
                        'status' => 'Tagihan Tertunda',
                        'vehicle' => $unpaidTransaksi->kendaraan?->plat_nomor ?? null,
                        'type' => 'OUT'
                    ],
                    'payment_required' => [
                        'id_parkir' => (int) $unpaidTransaksi->id_parkir,
                        'amount' => (float) $totalTagihan,
                        'methods' => [
                            // Sedang tidak mencukupi, jadi NestonPay (saldo) tidak bisa langsung dipakai
                            'nestonpay' => false,
                            'midtrans' => true,
                        ],
                    ],
                    'amount' => (float) $totalTagihan,
                ], 402);
            }
        }
        // --- END AUTO-PAYMENT ---

        if (!$kendaraan) {
            return response()->json([
                'success' => false,
                'message' => 'User belum memiliki kendaraan terdaftar!',
                'user' => [
                    'name' => $user->name,
                    'photo' => $user->profile_photo_url,
                    'balance' => $user->balance ?? $user->saldo ?? 0,
                    'status' => 'Data Kendaraan Kosong'
                ]
            ], 400);
        }

        // Cek apakah sedang parkir (transaksi dengan status 'masuk')
        $activeTransaksi = Transaksi::where('id_user', $user->id)
            ->where('status', 'masuk')
            ->latest('waktu_masuk')
            ->first();

        if (!$activeTransaksi) {
            // Check-in (ENTRY)
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
            // Check-out (EXIT + PAYMENT)
            $tarif = $activeTransaksi->tarif ?? Tarif::first();
            $rate = $tarif ? $tarif->tarif_perjam : 2000;

            $startTime = Carbon::parse($activeTransaksi->waktu_masuk);
            $endTime = now();
            $durationHours = max(1, ceil($startTime->diffInMinutes($endTime) / 60));
            $totalAmount = $durationHours * $rate;

            $currentBalance = $user->balance ?? $user->saldo ?? 0;

            if ($currentBalance < $totalAmount) {
                // Saat saldo kurang, tetap selesaikan check-out dan set status pembayaran menjadi pending
                // agar operator bisa langsung lanjutkan pembayaran (NestonPay/Midtrans).
                DB::transaction(function () use ($activeTransaksi, $totalAmount, $durationHours, $user) {
                    $activeTransaksi->update([
                        'waktu_keluar' => now(),
                        'durasi_jam' => $durationHours,
                        'biaya_total' => $totalAmount,
                        'status' => 'keluar',
                        'status_pembayaran' => 'pending',
                    ]);

                    // Legacy OUT transaction for history
                    Transaction::create([
                        'user_id' => $user->id,
                        'type' => 'OUT',
                        'amount' => $totalAmount,
                        'created_at' => now(),
                    ]);
                });

                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak cukup! Check-out selesai, silakan bayar Rp ' . number_format($totalAmount, 0, ',', '.'),
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->profile_photo_url,
                        'balance' => $currentBalance,
                        'status' => 'Menunggu Pembayaran',
                        'vehicle' => $kendaraan->plat_nomor,
                        'type' => 'OUT',
                    ],
                    'amount' => (float) $totalAmount,
                    'payment_required' => [
                        'id_parkir' => (int) $activeTransaksi->id_parkir,
                        'amount' => (float) $totalAmount,
                        'methods' => [
                            'nestonpay' => false,
                            'midtrans' => true,
                        ],
                    ],
                ], 402);
            }

            DB::transaction(function () use ($user, $activeTransaksi, $totalAmount, $durationHours) {
                // Update User Balance (Sync both columns)
                $user->decrement('balance', $totalAmount);
                $user->decrement('saldo', $totalAmount);

                // Update Transaksi record
                $activeTransaksi->update([
                    'waktu_keluar' => now(),
                    'durasi_jam' => $durationHours,
                    'biaya_total' => $totalAmount,
                    'status' => 'keluar',
                    'status_pembayaran' => 'berhasil'
                ]);

                // Create legacy OUT transaction for history
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'OUT',
                    'amount' => $totalAmount,
                    'created_at' => now(),
                ]);

                // Create legacy PAYMENT transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'PAYMENT',
                    'amount' => $totalAmount,
                    'created_at' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Check-out Berhasil',
                'user' => [
                    'name' => $user->name,
                    'photo' => $user->profile_photo_url,
                    'balance' => ($user->balance ?? $user->saldo ?? 0) - $totalAmount,
                    'status' => 'Parkir Keluar',
                    'vehicle' => $kendaraan->plat_nomor,
                    'type' => 'OUT'
                ],
                'amount' => $totalAmount
            ]);
        }
    }
}

