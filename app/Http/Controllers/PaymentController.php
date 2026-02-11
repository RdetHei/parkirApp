<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class PaymentController extends Controller
{
    /**
     * Halaman untuk memilih transaksi yang akan dibayar
     */
    public function selectTransaction()
    {
        // Hanya ambil transaksi yang sudah checkout (status 'keluar') dan pembayaran pending
        $transaksis = Transaksi::where('status', 'keluar')
            ->where(function($q) {
                // Tampilkan transaksi dengan status pembayaran pending atau belum ada
                $q->whereNull('status_pembayaran')
                  ->orWhere('status_pembayaran', '!=', 'berhasil');
            })
            ->with(['kendaraan', 'tarif', 'user', 'area'])
            ->orderBy('waktu_keluar', 'desc')
            ->get();

        return view('payment.select-transaction', compact('transaksis'));
    }

    /**
     * Tampil form pembayaran untuk transaksi
     */
    public function create($id_parkir)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
            ->findOrFail($id_parkir);

        // Cek apakah sudah ada pembayaran berhasil
        if ($transaksi->status_pembayaran === 'berhasil') {
            return back()->with('error', 'Transaksi ini sudah dibayar');
        }

        $qr_data = route('payment.confirm-qr', $id_parkir);

        return view('payment.create', compact('transaksi', 'qr_data'));
    }

    /**
     * Pembayaran Manual - Form Konfirmasi
     */
    public function manual_confirm($id_parkir)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
            ->findOrFail($id_parkir);

        if ($transaksi->status_pembayaran === 'berhasil') {
            return back()->with('error', 'Transaksi ini sudah dibayar');
        }

        return view('payment.manual-confirm', compact('transaksi'));
    }

    /**
     * Proses Pembayaran Manual
     */
    public function manual_process(Request $request, $id_parkir)
    {
        // Ambil transaksi untuk validasi awal (non-lock) supaya message bisa ditampilkan
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])->findOrFail($id_parkir);

        if ($transaksi->status !== 'keluar') {
            return back()->with('error', 'Pembayaran hanya bisa diproses setelah checkout.');
        }

        if (is_null($transaksi->biaya_total)) {
            return back()->with('error', 'Gagal memproses pembayaran: Biaya total tidak tersedia. Silakan hubungi administrator.');
        }

        $request->validate([
            // Nominal boleh berbeda dari biaya_total (misal diskon/koreksi manual).
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            // Gunakan transaction + row lock untuk mencegah double-payment
            DB::transaction(function () use ($id_parkir, $request) {
                // Re-fetch transaksi dengan lock
                $transaksi = Transaksi::lockForUpdate()->findOrFail($id_parkir);

                if ($transaksi->status_pembayaran === 'berhasil') {
                    // Jika sudah dibayar, batalkan
                    throw new \Exception('Transaksi ini sudah dibayar');
                }

                if ($transaksi->status !== 'keluar') {
                    throw new \Exception('Pembayaran hanya bisa diproses setelah checkout.');
                }

                if (is_null($transaksi->biaya_total)) {
                    throw new \Exception('Biaya total tidak tersedia.');
                }

                // Buat record pembayaran
                $pembayaran = Pembayaran::create([
                    'id_parkir' => $id_parkir,
                    'nominal' => $request->nominal,
                    'metode' => 'manual',
                    'status' => 'berhasil',
                    'keterangan' => $request->keterangan ?? 'Pembayaran manual oleh petugas',
                    'id_user' => Auth::id() ?? null,
                    'waktu_pembayaran' => Carbon::now(),
                ]);

                // Update transaksi
                $transaksi->update([
                    'status_pembayaran' => 'berhasil',
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                ]);
            });

            return redirect()->route('payment.success', $id_parkir)
                ->with('success', 'Pembayaran berhasil diproses');
        } catch (\Exception $e) {
            Log::error('manual_process pembayaran gagal', ['id_parkir' => $id_parkir, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Halaman pembayaran via QR Scan
     */
    public function qr_scan($id_parkir)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif'])
            ->findOrFail($id_parkir);

        if ($transaksi->status_pembayaran === 'berhasil') {
            return back()->with('error', 'Transaksi ini sudah dibayar');
        }

        // Buat temporary signed URL untuk konfirmasi QR (kedaluwarsa)
        $signedUrl = URL::temporarySignedRoute(
            'payment.confirm-qr.signed',
            now()->addMinutes(15),
            ['id_parkir' => $id_parkir]
        );

        return view('payment.qr-scan', compact('transaksi', 'signedUrl'));
    }

    /**
     * Konfirmasi pembayaran via QR Scan (signed URL - public)
     * Route is public but requires a valid signature.
     */
    public function confirm_qr_signed($id_parkir)
    {
        try {
            // Gunakan transaction + lock untuk mencegah double-payment
            DB::transaction(function () use ($id_parkir) {
                $transaksi = Transaksi::lockForUpdate()->findOrFail($id_parkir);

                if ($transaksi->status_pembayaran === 'berhasil') {
                    throw new \Exception('Transaksi ini sudah dibayar');
                }

                if ($transaksi->status !== 'keluar') {
                    throw new \Exception('Pembayaran hanya bisa diproses setelah checkout.');
                }

                if (is_null($transaksi->biaya_total)) {
                    throw new \Exception('Biaya total tidak tersedia.');
                }

                // Buat record pembayaran
                $pembayaran = Pembayaran::create([
                    'id_parkir' => $id_parkir,
                    'nominal' => $transaksi->biaya_total,
                    'metode' => 'qr_scan',
                    'status' => 'berhasil',
                    'keterangan' => 'Pembayaran otomatis via scan QR (signed URL)',
                    'id_user' => Auth::id() ?? null,
                    'waktu_pembayaran' => Carbon::now(),
                ]);

                // Update transaksi
                $transaksi->update([
                    'status_pembayaran' => 'berhasil',
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                ]);
            });

            // Redirect ke halaman thank-you publik (customer biasanya tidak login)
            return redirect()->route('payment.thank-you', $id_parkir)->with('success', 'Pembayaran berhasil diproses');
        } catch (\Exception $e) {
            Log::error('confirm_qr_signed gagal', ['id_parkir' => $id_parkir, 'error' => $e->getMessage()]);
            return redirect()->route('payment.create', $id_parkir)->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Halaman terima kasih publik setelah pembayaran via QR (tanpa login).
     */
    public function thankYou($id_parkir)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif'])
            ->where('id_parkir', $id_parkir)
            ->where('status_pembayaran', 'berhasil')
            ->firstOrFail();

        return view('payment.thank-you', compact('transaksi'));
    }

    /**
     * Konfirmasi pembayaran via QR Scan
     */
    public function confirm_qr($id_parkir)
    {
        try {
            // Endpoint ini dipakai oleh halaman petugas untuk polling status pembayaran.
            // Proses pembayaran QR sebenarnya dilakukan via signed URL (confirm_qr_signed).
            $transaksi = Transaksi::findOrFail($id_parkir);

            if ($transaksi->status_pembayaran === 'berhasil') {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran sudah berhasil',
                    'redirect' => route('payment.success', $id_parkir),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Menunggu pembayaran...',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman sukses pembayaran.
     * Jika transaksi belum tercatat berhasil dan ada midtrans_order_id, sinkron status dari API Midtrans
     * (agar pembayaran via Midtrans tetap tercatat meskipun notifikasi server tidak sampai, misal di localhost).
     */
    public function success($id_parkir)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'pembayaran', 'user', 'area'])
            ->findOrFail($id_parkir);

        if ($transaksi->status_pembayaran !== 'berhasil' && !empty($transaksi->midtrans_order_id)) {
            $this->syncMidtransPaymentStatus((int) $id_parkir);
            $transaksi->refresh();
            $transaksi->load(['kendaraan', 'tarif', 'pembayaran', 'user', 'area']);
        }

        return view('payment.success', compact('transaksi'));
    }

    /**
     * Riwayat pembayaran
     */
    public function index()
    {
        $pembayarans = Pembayaran::with(['transaksi', 'petugas'])
            ->orderBy('id_pembayaran', 'desc')
            ->paginate(15);

        return view('payment.index', compact('pembayarans'));
    }

    /**
     * Halaman pembayaran Midtrans (Snap).
     */
    public function midtransPay($id_parkir)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])
            ->findOrFail($id_parkir);

        if ($transaksi->status_pembayaran === 'berhasil') {
            return redirect()->route('payment.create', $id_parkir)->with('error', 'Transaksi ini sudah dibayar');
        }
        if ($transaksi->status !== 'keluar' || is_null($transaksi->biaya_total)) {
            return redirect()->route('payment.create', $id_parkir)->with('error', 'Transaksi tidak valid untuk pembayaran.');
        }

        $clientKey = config('services.midtrans.client_key');
        $isProduction = config('services.midtrans.is_production');
        return view('payment.midtrans-pay', compact('transaksi', 'clientKey', 'isProduction'));
    }

    /**
     * API: dapatkan Snap token untuk transaksi (dipanggil dari halaman Midtrans Pay).
     */
    public function midtransSnapToken(Request $request, $id_parkir)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif'])->findOrFail($id_parkir);

        if ($transaksi->status_pembayaran === 'berhasil') {
            return response()->json(['error' => 'Transaksi sudah dibayar'], 400);
        }
        if ($transaksi->status !== 'keluar' || is_null($transaksi->biaya_total)) {
            return response()->json(['error' => 'Transaksi tidak valid'], 400);
        }

        $serverKey = config('services.midtrans.server_key');
        $isProduction = config('services.midtrans.is_production');

        if (empty($serverKey)) {
            Log::error('Midtrans server key tidak di-set');
            return response()->json(['error' => 'Konfigurasi pembayaran belum tersedia'], 500);
        }

        try {
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = $isProduction;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $order_id = 'PARKIR-' . $id_parkir . '-' . time();
            $gross_amount = (int) round((float) $transaksi->biaya_total);

            // Simpan order_id agar halaman success bisa sinkron status dari API Midtrans jika notifikasi tidak sampai
            $transaksi->update(['midtrans_order_id' => $order_id]);

            $params = [
                'transaction_details' => [
                    'order_id' => $order_id,
                    'gross_amount' => $gross_amount,
                ],
                'item_details' => [
                    [
                        'id' => (string) $id_parkir,
                        'price' => $gross_amount,
                        'quantity' => 1,
                        'name' => 'Parkir - ' . ($transaksi->kendaraan->plat_nomor ?? 'Kendaraan'),
                        'category' => 'Parkir',
                    ],
                ],
                'customer_details' => [
                    'first_name' => $transaksi->kendaraan->pemilik ?? $transaksi->kendaraan->plat_nomor ?? 'Customer',
                    'email' => $transaksi->user?->email ?? 'customer@parked.local',
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $order_id,
            ]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Log::error('Midtrans getSnapToken error', ['id_parkir' => $id_parkir, 'message' => $msg]);
            if (str_contains($msg, '401') || str_contains(strtolower($msg), 'unauthorized') || str_contains($msg, 'Access denied')) {
                return response()->json(['error' => 'Konfigurasi Midtrans tidak valid (401). Periksa MIDTRANS_SERVER_KEY/MIDTRANS_CLIENT_KEY dan sesuaikan MIDTRANS_IS_PRODUCTION (sandbox/production). Lalu jalankan: php artisan config:clear dan php artisan midtrans:check'], 500);
            }
            return response()->json(['error' => 'Gagal membuat sesi pembayaran: ' . $msg], 500);
        }
    }

    /**
     * Callback notifikasi Midtrans (idempotent).
     * order_id format: PARKIR-{id_parkir}-{timestamp}
     * Data divalidasi dengan mengambil status transaksi dari API Midtrans (bukan hanya dari body POST).
     */
    public function midtransNotification(Request $request)
    {
        $payload = $request->all();
        $order_id = $payload['order_id'] ?? null;
        Log::info('Midtrans notification', ['order_id' => $order_id, 'transaction_status' => $payload['transaction_status'] ?? null]);

        if (!$order_id || !preg_match('/^PARKIR-(\d+)-/', $order_id, $m)) {
            return response()->json(['message' => 'Invalid order_id'], 400);
        }

        $id_parkir = (int) $m[1];

        // Verifikasi dengan mengambil status resmi dari API Midtrans (agar notifikasi benar-benar dari Midtrans)
        $serverKey = config('services.midtrans.server_key');
        $isProduction = config('services.midtrans.is_production');
        if (empty($serverKey)) {
            Log::error('Midtrans notification: server key tidak di-set');
            return response()->json(['message' => 'Server error'], 500);
        }

        try {
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = $isProduction;
            $statusResponse = \Midtrans\Transaction::status($order_id);
        } catch (\Exception $e) {
            Log::warning('Midtrans notification: gagal fetch status', ['order_id' => $order_id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid or unreachable transaction'], 400);
        }

        $transaction_status = $statusResponse->transaction_status ?? null;
        $transaction_status = is_string($transaction_status) ? strtolower($transaction_status) : $transaction_status;
        $transaction_id = $statusResponse->transaction_id ?? $payload['transaction_id'] ?? null;
        $payment_type = $statusResponse->payment_type ?? $payload['payment_type'] ?? null;
        $gross_amount = (float) ($statusResponse->gross_amount ?? $payload['gross_amount'] ?? 0);

        // Hanya proses settlement atau capture sebagai pembayaran berhasil
        $successStatuses = ['capture', 'settlement'];
        if (!in_array($transaction_status, $successStatuses)) {
            return response()->json(['received' => true]);
        }

        try {
            $this->applyMidtransSuccess($id_parkir, $order_id, $transaction_id, $payment_type, $gross_amount);
        } catch (\Exception $e) {
            Log::error('Midtrans notification handler error', ['order_id' => $order_id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Processing error'], 500);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Menerapkan pembayaran Midtrans berhasil: buat record Pembayaran dan update Transaksi (idempotent).
     */
    private function applyMidtransSuccess(int $id_parkir, string $order_id, ?string $transaction_id, ?string $payment_type, float $gross_amount): void
    {
        DB::transaction(function () use ($id_parkir, $order_id, $transaction_id, $payment_type, $gross_amount) {
            $transaksi = Transaksi::lockForUpdate()->find($id_parkir);
            if (!$transaksi) {
                return;
            }
            if ($transaksi->status_pembayaran === 'berhasil') {
                return;
            }
            if ($transaksi->status !== 'keluar' || is_null($transaksi->biaya_total)) {
                return;
            }

            $pembayaran = Pembayaran::create([
                'id_parkir' => $id_parkir,
                'order_id' => $order_id,
                'transaction_id' => $transaction_id,
                'payment_type' => $payment_type,
                'nominal' => $gross_amount,
                'metode' => 'midtrans',
                'status' => 'berhasil',
                'keterangan' => 'Pembayaran Midtrans (' . ($payment_type ?? 'online') . ')',
                'id_user' => null,
                'waktu_pembayaran' => Carbon::now(),
            ]);

            $transaksi->update([
                'status_pembayaran' => 'berhasil',
                'id_pembayaran' => $pembayaran->id_pembayaran,
            ]);
        });
    }

    /**
     * Sinkron status pembayaran dari API Midtrans (untuk kasus notifikasi server tidak sampai, misal localhost).
     * Dipanggil saat user membuka halaman success setelah bayar via Midtrans.
     */
    private function syncMidtransPaymentStatus(int $id_parkir): bool
    {
        $transaksi = Transaksi::find($id_parkir);
        if (!$transaksi || $transaksi->status_pembayaran === 'berhasil') {
            return false;
        }
        $order_id = $transaksi->midtrans_order_id;
        if (empty($order_id)) {
            return false;
        }

        $serverKey = config('services.midtrans.server_key');
        $isProduction = config('services.midtrans.is_production');
        if (empty($serverKey)) {
            return false;
        }

        try {
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = $isProduction;
            $statusResponse = \Midtrans\Transaction::status($order_id);
        } catch (\Exception $e) {
            Log::info('Midtrans sync status skip', ['order_id' => $order_id, 'reason' => $e->getMessage()]);
            return false;
        }

        $transaction_status = $statusResponse->transaction_status ?? null;
        $transaction_status = is_string($transaction_status) ? strtolower($transaction_status) : $transaction_status;
        $successStatuses = ['capture', 'settlement'];
        if (!in_array($transaction_status, $successStatuses)) {
            return false;
        }

        $transaction_id = $statusResponse->transaction_id ?? null;
        $payment_type = $statusResponse->payment_type ?? null;
        $gross_amount = (float) ($statusResponse->gross_amount ?? 0);

        $this->applyMidtransSuccess($id_parkir, $order_id, $transaction_id, $payment_type, $gross_amount);
        return true;
    }
}
