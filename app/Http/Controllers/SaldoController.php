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

    public function index()
    {
        $user = Auth::user();
        $histories = SaldoHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.saldo.index', compact('user', 'histories'));
    }

    public function topup()
    {
        return view('user.saldo.topup');
    }

    public function processPayWithSaldo(Request $request, $id_parkir)
    {
        try {
            $user = Auth::user();
            $transaksiPreview = Transaksi::with('kendaraan')->findOrFail($id_parkir);

            if (!in_array($user->role, ['admin', 'petugas', 'owner'], true) && (int) $transaksiPreview->id_user !== (int) $user->id) {
                abort(403);
            }

            return DB::transaction(function () use ($id_parkir, $user): \Illuminate\Http\RedirectResponse {
                $transaksi = Transaksi::lockForUpdate()->with('kendaraan')->findOrFail($id_parkir);

                // Validasi idempotent: jangan potong saldo ulang.
                if ($transaksi->status_pembayaran === 'berhasil') {
                    throw new \Exception('Transaksi ini sudah dibayar.');
                }

                $amount = (float) $transaksi->biaya_total;

                // Lock saldo user supaya aman terhadap double-click / parallel request.
                $userLocked = User::query()->where('id', $user->id)->lockForUpdate()->firstOrFail();

                if ((float) $userLocked->saldo < $amount) {
                    throw new \Exception('Saldo NestonPay tidak mencukupi. Silakan Top Up terlebih dahulu.');
                }

                // 1) Potong Saldo User
                $userLocked->saldo = (float) $userLocked->saldo - $amount;
                if (array_key_exists('balance', $userLocked->getAttributes())) {
                    $userLocked->balance = (float) ($userLocked->balance ?? 0) - $amount;
                }
                $userLocked->save();

                // 2) Catat Riwayat Saldo
                $saldoHistory = SaldoHistory::create([
                    'user_id' => $userLocked->id,
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
                    'id_user' => $userLocked->id, // Pembayaran diproses sendiri oleh user via saldo
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
            $user->saldo += $amount;
            if (array_key_exists('balance', $user->getAttributes())) {
                $user->balance = (float) ($user->balance ?? 0) + $amount;
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
}
