<?php

namespace App\Http\Controllers;

use App\Models\RfidTransaction;
use App\Models\SaldoHistory;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RfidParkingController extends Controller
{
    public function scanPage()
    {
        $title = 'RFID Parkir Scan';
        return view('parkir.scan', compact('title'));
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'rfid_uid' => ['required', 'string', 'max:128', 'regex:/^[0-9A-Za-z]+$/'],
        ]);

        $user = User::query()->where('rfid_uid', $data['rfid_uid'])->first();
        if (! $user) {
            return response()->json([
                'ok' => false,
                'error' => 'UID RFID tidak terdaftar.',
            ], 404);
        }

        $tarif = Tarif::query()->orderByDesc('id_tarif')->first();
        if (! $tarif) {
            return response()->json([
                'ok' => false,
                'error' => 'Tarif tidak ditemukan di sistem.',
            ], 500);
        }

        $result = DB::transaction(function () use ($user, $tarif) {
            $userLocked = User::query()->where('id', $user->id)->lockForUpdate()->firstOrFail();

            $inTx = RfidTransaction::query()
                ->where('user_id', $userLocked->id)
                ->where('type', 'IN')
                ->orderByDesc('created_at')
                ->first();

            $hasActiveSession = false;
            if ($inTx) {
                $closed = RfidTransaction::query()
                    ->where('user_id', $userLocked->id)
                    ->whereIn('type', ['OUT', 'PAYMENT'])
                    ->where('created_at', '>', $inTx->created_at)
                    ->exists();

                $hasActiveSession = ! $closed;
            }

            $userInfo = [
                'user_id' => (int) $userLocked->id,
                'name' => $userLocked->name,
                'photo' => $userLocked->photo,
                'saldo' => (float) ($userLocked->saldo ?? 0),
                'balance' => (float) ($userLocked->balance ?? $userLocked->saldo ?? 0),
            ];

            // CHECK-IN (IN): belum ada sesi aktif atau sesi sudah ditutup
            if (! $hasActiveSession) {
                $txIn = RfidTransaction::create([
                    'user_id' => $userLocked->id,
                    'type' => 'IN',
                    'amount' => null,
                    'created_at' => now(),
                ]);

                return response()->json([
                    'ok' => true,
                    'action' => 'IN',
                    'parkir_active' => true,
                    'user' => $userInfo,
                    'in_transaction_id' => (int) $txIn->id,
                    'message' => 'Check-in berhasil.',
                ]);
            }

            // CHECK-OUT (OUT + PAYMENT): ada sesi aktif
            $minutes = max(0, $inTx->created_at->diffInMinutes(now()));
            $hours = max(1, (int) ceil($minutes / 60));
            $amount = (float) $hours * (float) $tarif->tarif_perjam;

            $currentSaldo = (float) ($userLocked->saldo ?? 0);
            if ($currentSaldo < $amount) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Saldo tidak cukup.',
                    'required' => $amount,
                    'balance' => $currentSaldo,
                ], 402);
            }

            // Potong saldo (server-side validation)
            $userLocked->saldo = $currentSaldo - $amount;
            if (array_key_exists('balance', $userLocked->getAttributes())) {
                $userLocked->balance = (float) ($userLocked->balance ?? 0) - $amount;
            }
            $userLocked->save();

            SaldoHistory::create([
                'user_id' => $userLocked->id,
                'amount' => -$amount,
                'type' => 'payment',
                'description' => 'Pembayaran Parkir (RFID)',
                'reference_id' => null,
            ]);

            $outTx = RfidTransaction::create([
                'user_id' => $userLocked->id,
                'type' => 'OUT',
                'amount' => $amount,
                'created_at' => now(),
            ]);

            $payTx = RfidTransaction::create([
                'user_id' => $userLocked->id,
                'type' => 'PAYMENT',
                'amount' => $amount,
                'created_at' => now(),
            ]);

            $userInfo['saldo'] = (float) ($userLocked->saldo ?? 0);
            $userInfo['balance'] = (float) ($userLocked->balance ?? $userLocked->saldo ?? 0);

            return response()->json([
                'ok' => true,
                'action' => 'OUT',
                'parkir_active' => false,
                'user' => $userInfo,
                'hours' => $hours,
                'amount' => $amount,
                'out_transaction_id' => (int) $outTx->id,
                'payment_transaction_id' => (int) $payTx->id,
                'message' => 'Check-out berhasil. Pembayaran diproses.',
            ]);
        });

        return $result;
    }
}

