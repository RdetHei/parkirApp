<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Tarif;
use App\Models\User;
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

        // Cek apakah sedang parkir (transaksi IN terakhir belum ada OUT yang lebih baru)
        $lastIn = Transaction::where('user_id', $user->id)
            ->where('type', 'IN')
            ->latest('created_at')
            ->first();

        $lastOut = Transaction::where('user_id', $user->id)
            ->where('type', 'OUT')
            ->latest('created_at')
            ->first();

        $isParking = $lastIn && (!$lastOut || $lastOut->created_at < $lastIn->created_at);

        if (!$isParking) {
            // Check-in (IN)
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
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('images/default-user.png'),
                    'balance' => $user->balance,
                    'status' => 'Parkir Masuk'
                ]
            ]);
        } else {
            // Check-out (OUT + PAYMENT)
            $tarif = Tarif::first(); // Asumsi ada tarif default, atau sesuaikan logika tarif
            $rate = $tarif ? $tarif->tarif_perjam : 2000;
            
            $startTime = Carbon::parse($lastIn->created_at);
            $endTime = now();
            $durationHours = max(1, ceil($startTime->diffInMinutes($endTime) / 60));
            $totalAmount = $durationHours * $rate;

            if ($user->balance < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak cukup! Biaya: Rp ' . number_format($totalAmount, 0, ',', '.'),
                    'user' => [
                        'name' => $user->name,
                        'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('images/default-user.png'),
                        'balance' => $user->balance,
                        'status' => 'Saldo Kurang'
                    ]
                ], 400);
            }

            DB::transaction(function () use ($user, $totalAmount) {
                // Update Balance
                $user->decrement('balance', $totalAmount);

                // Create OUT transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'OUT',
                    'amount' => $totalAmount,
                    'created_at' => now(),
                ]);

                // Create PAYMENT transaction
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
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : asset('images/default-user.png'),
                    'balance' => $user->balance - $totalAmount,
                    'status' => 'Parkir Keluar'
                ],
                'amount' => $totalAmount
            ]);
        }
    }
}

