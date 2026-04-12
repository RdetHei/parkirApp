<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;

/**
 * Laporan pembayaran & transaksi — route saat ini khusus role owner (lihat routes/web.php).
 */
class ReportController extends Controller
{
    public function pembayaran(Request $request)
    {
        $query = Pembayaran::with(['transaksi', 'petugas']);
        $this->applyPembayaranFilters($request, $query);

        $total_nominal = (clone $query)->sum('nominal');
        $count_pembayaran = (clone $query)->count();
        $avg_nominal = $count_pembayaran > 0 ? $total_nominal / $count_pembayaran : 0;

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(15);
        $title = 'Laporan Pembayaran';

        return view('report.pembayaran', compact('pembayarans', 'total_nominal', 'count_pembayaran', 'avg_nominal', 'title'));
    }

    public function transaksi(Request $request)
    {
        $query = Transaksi::with(['kendaraan', 'tarif', 'user', 'area']);
        $this->applyTransaksiFilters($request, $query);

        $total_transaksi = (clone $query)->count();
        $total_biaya = (clone $query)->sum('biaya_total');
        $durasi_rata = (clone $query)->avg('durasi_jam');

        $transaksis = $query->orderBy('waktu_masuk', 'desc')->paginate(15);
        $title = 'Laporan Transaksi';

        return view('report.transaksi', compact('transaksis', 'total_transaksi', 'total_biaya', 'durasi_rata', 'title'));
    }

    public function exportPembayaranCSV(Request $request)
    {
        $query = Pembayaran::with(['transaksi', 'petugas']);
        $this->applyPembayaranFilters($request, $query);
        $pembayarans = $query->orderBy('created_at', 'desc')->get();

        $filename = 'pembayaran_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = static function () use ($pembayarans): void {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Transaksi', 'Nominal', 'Metode', 'Status', 'Petugas', 'Waktu Pembayaran', 'Dibuat']);

            foreach ($pembayarans as $row) {
                fputcsv($file, [
                    $row->id_pembayaran,
                    $row->id_parkir,
                    $row->nominal,
                    $row->metode,
                    $row->status,
                    $row->petugas?->name ?? '-',
                    $row->waktu_pembayaran?->format('Y-m-d H:i:s') ?? '-',
                    $row->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTransaksiCSV(Request $request)
    {
        $query = Transaksi::with(['kendaraan', 'tarif', 'user', 'area']);
        $this->applyTransaksiFilters($request, $query);
        $transaksis = $query->orderBy('waktu_masuk', 'desc')->get();

        $filename = 'transaksi_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = static function () use ($transaksis): void {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Plat Nomor', 'Area', 'Waktu Masuk', 'Waktu Keluar', 'Durasi (jam)', 'Biaya', 'Status', 'Pembayaran']);

            foreach ($transaksis as $row) {
                fputcsv($file, [
                    $row->id_parkir,
                    $row->kendaraan?->plat_nomor ?? '-',
                    $row->area?->nama_area ?? '-',
                    $row->waktu_masuk?->format('Y-m-d H:i:s') ?? '-',
                    $row->waktu_keluar?->format('Y-m-d H:i:s') ?? '-',
                    $row->durasi_jam ?? '-',
                    $row->biaya_total ?? '-',
                    $row->status,
                    $row->status_pembayaran ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function applyPembayaranFilters(Request $request, $query): void
    {
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('id_parkir', 'like', "%{$search}%");
        }
    }

    private function applyTransaksiFilters(Request $request, $query): void
    {
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_masuk', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_masuk', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('id_area')) {
            $query->where('id_area', $request->id_area);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('kendaraan', static function ($q) use ($search): void {
                $q->where('plat_nomor', 'like', "%{$search}%");
            });
        }
    }
}
