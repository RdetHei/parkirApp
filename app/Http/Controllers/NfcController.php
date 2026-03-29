<?php

namespace App\Http\Controllers;

use App\Models\NfcTransaction;
use App\Models\Tarif;
use App\Models\User;
use App\Models\SaldoHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NfcController extends Controller
{
    /**
     * POST /api/encrypt-id
     * Input: user_id
     * Output: encrypted string (Laravel Crypt)
     */
    public function encryptId(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:tb_user,id',
        ]);

        $encrypted = Crypt::encryptString((string) $data['user_id']);

        return response()->json([
            'encrypted_id' => $encrypted,
        ]);
    }

    /**
     * POST /api/nfc-write
     * Terima encrypted_id (opsional untuk logging).
     */
    public function nfcWrite(Request $request)
    {
        $data = $request->validate([
            'encrypted_id' => 'required|string',
            'nfc_uid' => 'nullable|string|max:128',
        ]);

        try {
            $userId = Crypt::decryptString($data['encrypted_id']);
            $user = User::find($userId);

            Log::info('NFC write', [
                'user_id' => $user?->id,
                'nfc_uid' => $data['nfc_uid'],
            ]);
        } catch (\Throwable $e) {
            Log::warning('NFC write invalid encrypted_id', [
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'ok' => true,
        ]);
    }

    /**
     * POST /api/nfc-scan
     * Input: encrypted_id
     * Output: user info untuk popup
     */
    public function nfcScan(Request $request)
    {
        $data = $request->validate([
            'encrypted_id' => 'nullable|string|required_without:nfc_uid',
            'nfc_uid' => 'nullable|string|max:128|required_without:encrypted_id',
        ]);

        $user = null;
        if (!empty($data['nfc_uid'])) {
            $user = User::where('nfc_uid', $data['nfc_uid'])->first();
        }

        if (!$user && !empty($data['encrypted_id'])) {
            try {
                $userId = Crypt::decryptString($data['encrypted_id']);
                $user = User::find($userId);
            } catch (\Throwable $e) {
                return response()->json([
                    'error' => 'Encrypted id tidak valid',
                ], 422);
            }
        }

        if (! $user) {
            return response()->json([
                'error' => 'User tidak ditemukan',
            ], 404);
        }

        // VIP/Biasa: gunakan role 'vip' kalau ada, selain itu biasa.
        $status = ($user->role ?? 'user') === 'vip' ? 'VIP' : 'Biasa';

        $balance = (float) ($user->balance ?? $user->saldo ?? 0);

        return response()->json([
            'user_id' => (int) $user->id,
            'name' => $user->name,
            'photo' => $user->photo,
            'balance' => $balance,
            'status' => $status,
        ]);
    }

    /**
     * POST /api/parkir/masuk
     * Input: user_id (atau encrypted_id)
     * Output: transaksi IN
     */
    public function parkirMasuk(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|integer|exists:tb_user,id',
            'encrypted_id' => 'nullable|string',
            'nfc_uid' => 'nullable|string|max:128',
        ]);

        $userId = $this->resolveUserId($data);
        if (! $userId) {
            return response()->json(['error' => 'User tidak valid'], 422);
        }

        $inAlreadyActive = NfcTransaction::where('user_id', $userId)
            ->where('type', 'IN')
            ->orderByDesc('created_at')
            ->first();

        // Simpel: kalau transaksi IN terakhir belum diikuti OUT/PAYMENT, anggap masih aktif.
        if ($inAlreadyActive) {
            $closed = NfcTransaction::where('user_id', $userId)
                ->whereIn('type', ['OUT', 'PAYMENT'])
                ->where('created_at', '>', $inAlreadyActive->created_at)
                ->exists();

            if (! $closed) {
                return response()->json([
                    'error' => 'Sesi parkir NFC masih aktif (belum ada OUT).',
                ], 409);
            }
        }

        $tx = DB::transaction(function () use ($userId) {
            return NfcTransaction::create([
                'user_id' => $userId,
                'type' => 'IN',
                'amount' => 0,
                'created_at' => now(),
            ]);
        });

        $user = User::findOrFail($userId);

        return response()->json([
            'ok' => true,
            'transaction_id' => $tx->id,
            'user' => [
                'user_id' => (int) $user->id,
                'name' => $user->name,
                'photo' => $user->photo,
                'balance' => (float) ($user->balance ?? $user->saldo ?? 0),
                'status' => (($user->role ?? 'user') === 'vip') ? 'VIP' : 'Biasa',
            ],
        ]);
    }

    /**
     * POST /api/parkir/keluar
     * Input: user_id (atau encrypted_id)
     * Output: transaksi OUT + PAYMENT dan potong saldo
     */
    public function parkirKeluar(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|integer|exists:tb_user,id',
            'encrypted_id' => 'nullable|string',
            'nfc_uid' => 'nullable|string|max:128',
            // Opsional jika ingin menentukan tarif dari UI
            'id_tarif' => 'nullable|integer|exists:tb_tarif,id_tarif',
        ]);

        $userId = $this->resolveUserId($data);
        if (! $userId) {
            return response()->json(['error' => 'User tidak valid'], 422);
        }

        $user = User::query()->findOrFail($userId);

        $tarif = null;
        if (!empty($data['id_tarif'])) {
            $tarif = Tarif::find($data['id_tarif']);
        }
        if (! $tarif) {
            $tarif = Tarif::orderByDesc('id_tarif')->first();
        }

        if (! $tarif) {
            return response()->json(['error' => 'Tarif tidak ditemukan di sistem.'], 500);
        }

        $amount = 0;
        $inTx = null;

        $result = DB::transaction(function () use ($userId, $user, $tarif, &$amount, &$inTx) {
            $inTx = NfcTransaction::where('user_id', $userId)
                ->where('type', 'IN')
                ->orderByDesc('created_at')
                ->first();

            if (! $inTx) {
                return response()->json(['error' => 'Belum ada sesi IN.'], 404);
            }

            $closed = NfcTransaction::where('user_id', $userId)
                ->whereIn('type', ['OUT', 'PAYMENT'])
                ->where('created_at', '>', $inTx->created_at)
                ->exists();

            if ($closed) {
                return response()->json(['error' => 'Sesi sudah ditutup.'], 409);
            }

            $minutes = max(0, $inTx->created_at->diffInMinutes(now()));
            // Hitung jam (dibulatkan ke atas minimal 1 jam)
            $hours = max(1, (int) ceil($minutes / 60));

            $amount = (float) $hours * (float) $tarif->tarif_perjam;

            // Lock user untuk mencegah race condition potong saldo
            $userLocked = User::where('id', $userId)->lockForUpdate()->firstOrFail();
            $currentSaldo = (float) ($userLocked->saldo ?? 0);

            if ($currentSaldo < $amount) {
                return response()->json([
                    'error' => 'Saldo tidak cukup',
                    'required' => $amount,
                    'balance' => $currentSaldo,
                ], 402);
            }

            $userLocked->saldo = $currentSaldo - $amount;

            // Jika kolom balance ada, sinkronkan juga.
            if (array_key_exists('balance', $userLocked->getAttributes())) {
                $userLocked->balance = (float) ($userLocked->balance ?? 0) - $amount;
            }
            $userLocked->save();

            SaldoHistory::create([
                'user_id' => $userId,
                'amount' => -$amount,
                'type' => 'payment',
                'description' => 'Pembayaran Parkir (NFC)',
                'reference_id' => null,
            ]);

            $outTx = NfcTransaction::create([
                'user_id' => $userId,
                'type' => 'OUT',
                'amount' => $amount,
                'created_at' => now(),
            ]);

            $payTx = NfcTransaction::create([
                'user_id' => $userId,
                'type' => 'PAYMENT',
                'amount' => $amount,
                'created_at' => now(),
            ]);

            return response()->json([
                'ok' => true,
                'hours' => $hours,
                'amount' => $amount,
                'out_transaction_id' => $outTx->id,
                'payment_transaction_id' => $payTx->id,
                'balance' => (float) $userLocked->saldo,
            ]);
        });

        return $result;
    }

    private function resolveUserId(array $data): ?int
    {
        if (!empty($data['nfc_uid'])) {
            $id = User::where('nfc_uid', $data['nfc_uid'])->value('id');
            if ($id) {
                return (int) $id;
            }
        }

        if (!empty($data['encrypted_id'])) {
            try {
                return (int) Crypt::decryptString($data['encrypted_id']);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return isset($data['user_id']) ? (int) $data['user_id'] : null;
    }
}

