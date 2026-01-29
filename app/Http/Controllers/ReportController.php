<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Report pembayaran dengan filter
     */
    public function pembayaran(Request $request)
    {
        $query = Pembayaran::with(['transaksi', 'petugas']);

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by metode
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Summary statistics
        $total_nominal = (clone $query)->sum('nominal');
        $count_pembayaran = (clone $query)->count();
        $avg_nominal = $count_pembayaran > 0 ? $total_nominal / $count_pembayaran : 0;
        $title = 'Laporan Pembayaran';
        return view('report.pembayaran', compact('pembayarans', 'total_nominal', 'count_pembayaran', 'avg_nominal', 'title'));
    }

    /**
     * Report transaksi dengan filter
     */
    public function transaksi(Request $request)
    {
        $query = Transaksi::with(['kendaraan', 'tarif', 'user', 'area']);

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_masuk', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_masuk', '<=', $request->tanggal_sampai);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by area
        if ($request->filled('id_area')) {
            $query->where('id_area', $request->id_area);
        }

        $transaksis = $query->orderBy('waktu_masuk', 'desc')->paginate(15);

        // Summary statistics
        $total_transaksi = (clone $query)->count();
        $total_biaya = (clone $query)->sum('biaya_total');
        $durasi_rata = (clone $query)->avg('durasi_jam');
        $title = 'Laporan Transaksi';
        return view('report.transaksi', compact('transaksis', 'total_transaksi', 'total_biaya', 'durasi_rata', 'title'));
    }

    /**
     * Export pembayaran ke CSV
     */
    public function exportPembayaranCSV(Request $request)
    {
        $query = Pembayaran::with(['transaksi', 'petugas']);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->get();

        $filename = 'pembayaran_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = array(
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $callback = function() use ($pembayarans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array('ID', 'Transaksi', 'Nominal', 'Metode', 'Status', 'Petugas', 'Waktu Pembayaran', 'Dibuat'));

            foreach ($pembayarans as $row) {
                fputcsv($file, array(
                    $row->id_pembayaran,
                    $row->id_parkir,
                    $row->nominal,
                    $row->metode,
                    $row->status,
                    $row->petugas?->name ?? '-',
                    $row->waktu_pembayaran?->format('Y-m-d H:i:s') ?? '-',
                    $row->created_at->format('Y-m-d H:i:s'),
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export transaksi ke CSV
     */
    public function exportTransaksiCSV(Request $request)
    {
        $query = Transaksi::with(['kendaraan', 'tarif', 'user', 'area']);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_masuk', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_masuk', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transaksis = $query->orderBy('waktu_masuk', 'desc')->get();

        $headers = array(
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Accept-Language' => "id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7",
        );

        $callback = function() use ($transaksis) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array('ID', 'Plat Nomor', 'Area', 'Waktu Masuk', 'Waktu Keluar', 'Durasi (jam)', 'Biaya', 'Status', 'Pembayaran'));

            foreach ($transaksis as $row) {
                fputcsv($file, array(
                    $row->id_parkir,
                    $row->kendaraan?->plat_nomor ?? '-',
                    $row->area?->nama_area ?? '-',
                    $row->waktu_masuk?->format('Y-m-d H:i:s') ?? '-',
                    $row->waktu_keluar?->format('Y-m-d H:i:s') ?? '-',
                    $row->durasi_jam ?? '-',
                    $row->biaya_total ?? '-',
                    $row->status,
                    $row->status_pembayaran ?? '-',
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
