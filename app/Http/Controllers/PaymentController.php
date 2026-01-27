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
        if (is_null($transaksi->biaya_total)) {
            return back()->with('error', 'Gagal memproses pembayaran: Biaya total tidak tersedia. Silakan hubungi administrator.');
        }

        $request->validate([
            'nominal' => 'required|numeric|min:' . ($transaksi->biaya_total ?? 0),
            'keterangan' => 'nullable|string|max:500',
        ], [
            'nominal.min' => 'Nominal pembayaran harus minimal Rp ' . number_format($transaksi->biaya_total ?? 0, 0, ',', '.'),
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

            return redirect()->route('payment.success', $id_parkir)->with('success', 'Pembayaran berhasil diproses');
        } catch (\Exception $e) {
            Log::error('confirm_qr_signed gagal', ['id_parkir' => $id_parkir, 'error' => $e->getMessage()]);
            return redirect()->route('payment.create', $id_parkir)->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi pembayaran via QR Scan
     */
    public function confirm_qr($id_parkir)
    {
        try {
            // Gunakan transaction + lock untuk mencegah double-payment
            DB::transaction(function () use ($id_parkir, &$response) {
                $transaksi = Transaksi::lockForUpdate()->findOrFail($id_parkir);

                if ($transaksi->status_pembayaran === 'berhasil') {
                    throw new \Exception('Transaksi ini sudah dibayar');
                }

                // Buat record pembayaran
                $pembayaran = Pembayaran::create([
                    'id_parkir' => $id_parkir,
                    'nominal' => $transaksi->biaya_total,
                    'metode' => 'qr_scan',
                    'status' => 'berhasil',
                    'keterangan' => 'Pembayaran otomatis via scan QR oleh pengendara',
                    'id_user' => Auth::id() ?? null,
                    'waktu_pembayaran' => Carbon::now(),
                ]);

                // Update transaksi
                $transaksi->update([
                    'status_pembayaran' => 'berhasil',
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses',
                'redirect' => route('payment.success', $id_parkir)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman sukses pembayaran
     */
    public function success($id_parkir)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'pembayaran', 'user', 'area'])
            ->findOrFail($id_parkir);

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
}
