<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Halaman untuk memilih transaksi yang akan dibayar
     */
    public function selectTransaction()
    {
        // Hanya ambil transaksi yang sedang 'masuk' dan belum dibayar
        $transaksis = Transaksi::where('status', 'masuk')
            ->where(function($q) {
                $q->whereNull('status_pembayaran')
                  ->orWhere('status_pembayaran', '<>', 'sudah_bayar');
            })
            ->with(['kendaraan', 'tarif', 'user', 'area'])
            ->orderBy('waktu_masuk', 'desc')
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

        // Cek apakah sudah ada pembayaran
        if ($transaksi->status_pembayaran === 'sudah_bayar') {
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

        if ($transaksi->status_pembayaran === 'sudah_bayar') {
            return back()->with('error', 'Transaksi ini sudah dibayar');
        }

        return view('payment.manual-confirm', compact('transaksi'));
    }

    /**
     * Proses Pembayaran Manual
     */
    public function manual_process(Request $request, $id_parkir)
    {
        $transaksi = Transaksi::findOrFail($id_parkir);

        $request->validate([
            'nominal' => 'required|numeric|min:' . ($transaksi->biaya_total ?? 0),
            'keterangan' => 'nullable|string|max:500',
        ], [
            'nominal.min' => 'Nominal pembayaran harus minimal Rp ' . number_format($transaksi->biaya_total ?? 0, 0, ',', '.'),
        ]);

        try {
            if ($transaksi->status_pembayaran === 'sudah_bayar') {
                return back()->with('error', 'Transaksi ini sudah dibayar');
            }

            DB::transaction(function () use ($id_parkir, $request, $transaksi) {
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
                    'status_pembayaran' => 'sudah_bayar',
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

        if ($transaksi->status_pembayaran === 'sudah_bayar') {
            return back()->with('error', 'Transaksi ini sudah dibayar');
        }

        return view('payment.qr-scan', compact('transaksi'));
    }

    /**
     * Konfirmasi pembayaran via QR Scan
     */
    public function confirm_qr($id_parkir)
    {
        try {
            $transaksi = Transaksi::findOrFail($id_parkir);

            if ($transaksi->status_pembayaran === 'sudah_bayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi ini sudah dibayar'
                ]);
            }


            DB::transaction(function () use ($id_parkir, $transaksi) {
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
                    'status_pembayaran' => 'sudah_bayar',
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
