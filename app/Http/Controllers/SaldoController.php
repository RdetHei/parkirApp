<?php

namespace App\Http\Controllers;

use App\Models\SaldoHistory;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Traits\LogsActivity;

class SaldoController extends Controller
{
    use LogsActivity;

    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    public function index()
    {
        $user = Auth::user();

        // Fallback for localhost: Midtrans notification (webhook) may not reach the app.
        // If user returns from Snap with an order_id, try to sync status and apply topup idempotently.
        $orderId = request()->query('order_id');
        if (is_string($orderId) && str_starts_with($orderId, 'TOPUP-')) {
            try {
                $this->syncTopupFromMidtrans($user, $orderId);
            } catch (\Throwable $e) {
                Log::info('Topup sync skipped', ['order_id' => $orderId, 'reason' => $e->getMessage()]);
            }
        }

        $histories = SaldoHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.saldo.index', compact('user', 'histories'));
    }

    public function topup()
    {
        $user = Auth::user();
        return view('user.saldo.topup', compact('user'));
    }

    /**
     * Get Midtrans Snap Token for Top Up
     */
    public function midtransSnapToken(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;
        $order_id = 'TOPUP-' . $user->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'item_details' => [
                [
                    'id' => 'TOPUP-' . $user->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Top Up Saldo NestonPay',
                    'category' => 'Wallet',
                ]
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $order_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans TopUp SnapToken Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat sesi pembayaran: ' . $e->getMessage()], 500);
        }
    }

    public function processPayWithSaldo(Request $request, $id_parkir)
    {
        try {
            $user = Auth::user();
            $transaksiPreview = Transaksi::with('kendaraan')->findOrFail($id_parkir);

            if (! in_array($user->role, ['admin', 'petugas', 'owner'], true) && (int) $transaksiPreview->id_user !== (int) $user->id) {
                abort(403);
            }

            return DB::transaction(function () use ($id_parkir, $user): \Illuminate\Http\RedirectResponse {
                $transaksi = Transaksi::lockForUpdate()->with('kendaraan')->findOrFail($id_parkir);

                // Validasi idempotent: jangan potong saldo ulang.
                if ($transaksi->status_pembayaran === 'berhasil') {
                    throw new \Exception('Transaksi ini sudah dibayar.');
                }

                $amount = (float) $transaksi->biaya_total;

                // Lock saldo user pemilik parkir (BUKAN petugas yang memproses)
                if (!$transaksi->id_user) {
                    throw new \Exception('Transaksi ini tidak terikat dengan akun user (Guest). Tidak dapat menggunakan NestonPay.');
                }

                $userTarget = User::query()->where('id', $transaksi->id_user)->lockForUpdate()->firstOrFail();

                $currentBalance = (float) ($userTarget->balance ?? $userTarget->saldo ?? 0);

                if ($currentBalance < $amount) {
                    throw new \Exception('Saldo NestonPay user tidak mencukupi. Silakan informasikan user untuk Top Up.');
                }

                // 1) Potong Saldo User Target
                if (array_key_exists('balance', $userTarget->getAttributes())) {
                    $userTarget->balance = $currentBalance - $amount;
                }
                if (array_key_exists('saldo', $userTarget->getAttributes())) {
                    $userTarget->saldo = (float) $userTarget->saldo - $amount;
                }
                $userTarget->save();

                // 2) Catat Riwayat Saldo untuk User Target
                $saldoHistory = SaldoHistory::create([
                    'user_id' => $userTarget->id,
                    'amount' => -$amount,
                    'type' => 'payment',
                    'description' => 'Pembayaran Parkir - ' . ($transaksi->kendaraan->plat_nomor ?? 'Kendaraan'),
                    'reference_id' => (string) $transaksi->id_parkir,
                ]);

                // 3) Catat di tabel Pembayaran
                $pembayaran = Pembayaran::create([
                    'id_parkir' => $transaksi->id_parkir,
                    'nominal' => $amount,
                    'metode' => 'nestonpay',
                    'status' => 'berhasil',
                    'id_user' => $user->id, // Petugas/Admin yang memproses (jika ada)
                    'waktu_pembayaran' => now(),
                ]);

                // 4) Update status transaksi
                $transaksi->update([
                    'status_pembayaran' => 'berhasil',
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                ]);

                $this->logActivity(
                    "Pembayaran NestonPay berhasil untuk transaksi #{$id_parkir}",
                    'transaksi',
                    $pembayaran,
                    [
                        'nominal' => $amount,
                        'plat_nomor' => $transaksi->kendaraan->plat_nomor,
                        'saldo_history_id' => $saldoHistory->id,
                    ]
                );

                return redirect()
                    ->route('payment.success', $transaksi->id_parkir)
                    ->with('success', 'Pembayaran menggunakan NestonPay berhasil!');
            });
        } catch (\Exception $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e; // preserve abort(403) response
            }
            Log::error('NestonPay payment error', ['id_parkir' => $id_parkir, 'message' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }

    // Simulasi Topup (untuk demo/development)
    public function storeTopupManual(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        DB::beginTransaction();
        try {
            if (array_key_exists('balance', $user->getAttributes())) {
                $user->balance = (float) ($user->balance ?? 0) + $amount;
            }
            if (array_key_exists('saldo', $user->getAttributes())) {
                $user->saldo = (float) ($user->saldo ?? 0) + $amount;
            }
            $user->save();

            $history = SaldoHistory::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'topup',
                'description' => 'Top Up Saldo NestonPay (Manual)',
            ]);

            $this->logActivity(
                "Topup saldo manual berhasil",
                'transaksi',
                $history,
                ['nominal' => $amount]
            );

            DB::commit();
            return redirect()->route('user.saldo.index')->with('success', 'Top Up sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses top up.');
        }
    }

    /**
     * Sync Midtrans topup status and apply credit (idempotent).
     */
    private function syncTopupFromMidtrans(User $user, string $orderId): void
    {
        if (! preg_match('/^TOPUP-(\d+)-/', $orderId, $m)) {
            return;
        }

        $userIdFromOrder = (int) $m[1];
        if ((int) $user->id !== $userIdFromOrder) {
            return;
        }

        // Already applied?
        if (SaldoHistory::where('reference_id', $orderId)->exists()) {
            return;
        }

        $serverKey = config('services.midtrans.server_key');
        if (empty($serverKey)) {
            return;
        }

        try {
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
            $status = \Midtrans\Transaction::status($orderId);
        } catch (\Throwable $e) {
            Log::info('Midtrans topup status fetch failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return;
        }

        $transactionStatus = strtolower((string) ($status->transaction_status ?? ''));
        $paymentType = $status->payment_type ?? null;
        $grossAmount = (float) ($status->gross_amount ?? 0);

        if (! in_array($transactionStatus, ['capture', 'settlement'], true)) {
            return;
        }
        if ($grossAmount <= 0) {
            return;
        }

        DB::transaction(function () use ($user, $orderId, $grossAmount, $paymentType) {
            if (SaldoHistory::where('reference_id', $orderId)->exists()) {
                return;
            }

            $userLocked = User::lockForUpdate()->findOrFail($user->id);
            if (array_key_exists('balance', $userLocked->getAttributes())) {
                $userLocked->balance = (float) ($userLocked->balance ?? 0) + $grossAmount;
            }
            if (array_key_exists('saldo', $userLocked->getAttributes())) {
                $userLocked->saldo = (float) ($userLocked->saldo ?? 0) + $grossAmount;
            }
            $userLocked->save();

            $history = SaldoHistory::create([
                'user_id' => $userLocked->id,
                'amount' => $grossAmount,
                'type' => 'topup',
                'description' => 'Top Up NestonPay via Midtrans (' . ($paymentType ?? 'online') . ')',
                'reference_id' => $orderId,
            ]);

            $this->logActivity(
                'Topup saldo Midtrans berhasil (sync)',
                'user',
                $userLocked,
                ['nominal' => $grossAmount, 'order_id' => $orderId, 'saldo_history_id' => $history->id]
            );
        });
    }
}
