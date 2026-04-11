<?php

namespace App\Http\Controllers;

use App\Models\KasShift;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CashPaymentController extends Controller
{
    use LogsActivity;

    private const METODE_CASH = 'cash';

    private const STAFF_ROLES = ['admin', 'petugas'];

    private function ensureStaff(): void
    {
        $user = Auth::user();
        if (! $user || ! in_array($user->role, self::STAFF_ROLES, true)) {
            abort(403);
        }
    }

    /**
     * Buka shift kas. Satu shift terbuka per (user, area).
     */
    public function openShift(Request $request): RedirectResponse|JsonResponse
    {
        $this->ensureStaff();

        $data = $request->validate([
            'id_area' => 'nullable|exists:tb_area_parkir,id_area',
            'opening_cash_amount' => 'nullable|numeric|min:0',
        ]);

        $userId = (int) Auth::id();
        $areaId = isset($data['id_area']) ? (int) $data['id_area'] : null;

        if (KasShift::openShiftFor($userId, $areaId)) {
            throw ValidationException::withMessages([
                'shift' => ['Shift masih terbuka untuk area ini. Tutup terlebih dahulu.'],
            ]);
        }

        $shift = KasShift::create([
            'opened_by' => $userId,
            'id_area' => $areaId,
            'opened_at' => now(),
            'opening_cash_amount' => $data['opening_cash_amount'] ?? null,
        ]);

        $this->logActivity(
            "Buka kas shift #{$shift->id_kas_shift}",
            'kas_shift',
            $shift,
            ['id_area' => $areaId]
        );

        if ($request->wantsJson()) {
            return response()->json(['shift' => $shift]);
        }

        return back()->with('success', 'Shift kas dibuka.');
    }

    /**
     * Tutup shift: setelah ini pembayaran tunai pada shift tersebut tidak boleh diubah.
     */
    public function closeShift(Request $request, int $id_kas_shift): RedirectResponse|JsonResponse
    {
        $this->ensureStaff();

        $data = $request->validate([
            'closing_note' => 'nullable|string|max:2000',
        ]);

        $shift = KasShift::query()->where('id_kas_shift', $id_kas_shift)->firstOrFail();

        if (Auth::user()->role !== 'admin' && (int) $shift->opened_by !== (int) Auth::id()) {
            abort(403);
        }

        if ($shift->isClosed()) {
            throw ValidationException::withMessages([
                'shift' => ['Shift sudah ditutup.'],
            ]);
        }

        $shift->update([
            'closed_at' => now(),
            'closed_by' => Auth::id(),
            'closing_note' => $data['closing_note'] ?? null,
        ]);

        $this->logActivity(
            "Tutup kas shift #{$shift->id_kas_shift}",
            'kas_shift',
            $shift,
            []
        );

        if ($request->wantsJson()) {
            return response()->json(['shift' => $shift->fresh()]);
        }

        return back()->with('success', 'Shift kas ditutup. Data tunai pada shift ini terkunci.');
    }

    /**
     * Buat intent pembayaran tunai (tb_pembayaran pending). Transaksi.status_pembayaran tetap pending.
     */
    public function initiate(Request $request, int $id_parkir): RedirectResponse|JsonResponse
    {
        $this->ensureStaff();

        $transaksi = Transaksi::query()->findOrFail($id_parkir);

        $this->ensureStaffCanAccessTransaksi($transaksi);

        if ($transaksi->status !== 'keluar' || $transaksi->biaya_total === null) {
            throw ValidationException::withMessages([
                'transaksi' => ['Transaksi harus sudah checkout dan memiliki biaya.'],
            ]);
        }

        if ($transaksi->status_pembayaran === 'berhasil') {
            throw ValidationException::withMessages([
                'transaksi' => ['Transaksi sudah lunas.'],
            ]);
        }

        $shift = KasShift::openShiftFor((int) Auth::id(), $transaksi->id_area ? (int) $transaksi->id_area : null);
        if (! $shift) {
            throw ValidationException::withMessages([
                'shift' => ['Buka shift kas terlebih dahulu untuk area transaksi ini.'],
            ]);
        }

        $pembayaran = DB::transaction(function () use ($transaksi, $shift): Pembayaran {
            $locked = Transaksi::lockForUpdate()->findOrFail($transaksi->id_parkir);

            if ($locked->status_pembayaran === 'berhasil') {
                throw ValidationException::withMessages(['transaksi' => ['Transaksi sudah lunas.']]);
            }

            $pendingExists = Pembayaran::query()
                ->where('id_parkir', $locked->id_parkir)
                ->where('metode', self::METODE_CASH)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->exists();

            if ($pendingExists) {
                throw ValidationException::withMessages([
                    'pembayaran' => ['Sudah ada pembayaran tunai pending untuk transaksi ini.'],
                ]);
            }

            $nominal = (float) $locked->biaya_total;
            $orderId = 'CASH-' . $locked->id_parkir . '-' . Str::lower(Str::uuid());

            return Pembayaran::create([
                'id_parkir' => $locked->id_parkir,
                'order_id' => $orderId,
                'nominal' => $nominal,
                'metode' => self::METODE_CASH,
                'status' => 'pending',
                'keterangan' => 'Menunggu konfirmasi petugas (tunai)',
                'id_kas_shift' => $shift->id_kas_shift,
                'id_user' => null,
                'waktu_pembayaran' => null,
            ]);
        });

        $this->logActivity(
            "Intent tunai transaksi #{$id_parkir} (pembayaran #{$pembayaran->id_pembayaran})",
            'pembayaran',
            $pembayaran,
            ['nominal' => $pembayaran->nominal]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'pembayaran' => $pembayaran,
                'message' => 'Silakan konfirmasi setelah uang diterima.',
            ]);
        }

        return back()->with('success', 'Transaksi ditandai bayar tunai. Konfirmasi setelah uang diterima.');
    }

    /**
     * Konfirmasi petugas: validasi uang diterima >= nominal (dari DB), set paid (waktu_pembayaran = paid_at).
     */
    public function confirm(Request $request): RedirectResponse|JsonResponse
    {
        $this->ensureStaff();

        $data = $request->validate([
            'id_pembayaran' => 'required|integer|exists:tb_pembayaran,id_pembayaran',
            'cash_received' => 'required|numeric|min:0',
        ]);

        $cashReceived = (float) $data['cash_received'];

        $result = DB::transaction(function () use ($data, $cashReceived): array {
            $pembayaran = Pembayaran::lockForUpdate()->findOrFail($data['id_pembayaran']);

            if ($pembayaran->metode !== self::METODE_CASH || $pembayaran->status !== 'pending') {
                throw ValidationException::withMessages([
                    'id_pembayaran' => ['Bukan pembayaran tunai pending.'],
                ]);
            }

            if ($pembayaran->id_kas_shift) {
                $shift = KasShift::lockForUpdate()->findOrFail($pembayaran->id_kas_shift);
                if ($shift->isClosed()) {
                    throw ValidationException::withMessages([
                        'shift' => ['Shift sudah ditutup; pembayaran tunai tidak dapat dikonfirmasi.'],
                    ]);
                }
            }

            $transaksi = Transaksi::lockForUpdate()->findOrFail($pembayaran->id_parkir);

            if ($transaksi->status_pembayaran === 'berhasil') {
                throw ValidationException::withMessages([
                    'transaksi' => ['Transaksi sudah lunas dengan metode lain.'],
                ]);
            }

            $amountDue = (float) $transaksi->biaya_total;
            $recordedNominal = (float) $pembayaran->nominal;

            if (abs($amountDue - $recordedNominal) > 0.01) {
                throw ValidationException::withMessages([
                    'transaksi' => [
                        'Nominal pembayaran tidak sama dengan biaya transaksi terkini. Batalkan intent dan buat ulang.',
                    ],
                ]);
            }

            if ($cashReceived + 0.00001 < $amountDue) {
                throw ValidationException::withMessages([
                    'cash_received' => ['Uang diterima harus lebih besar atau sama dengan total bayar.'],
                ]);
            }

            $change = round($cashReceived - $amountDue, 2);
            $paidAt = now();

            $pembayaran->update([
                'status' => 'berhasil',
                'cash_received' => $cashReceived,
                'cash_change' => $change,
                'id_user' => Auth::id(),
                'waktu_pembayaran' => $paidAt,
                'keterangan' => 'Pembayaran tunai (dikonfirmasi petugas)',
            ]);

            $transaksi->update([
                'status_pembayaran' => 'berhasil',
                'id_pembayaran' => $pembayaran->id_pembayaran,
            ]);

            return [
                'pembayaran' => $pembayaran->fresh(),
                'transaksi' => $transaksi->fresh(),
                'paid_at' => $paidAt->toIso8601String(),
            ];
        });

        $this->logActivity(
            "Konfirmasi tunai pembayaran #{$data['id_pembayaran']}",
            'pembayaran',
            $result['pembayaran'],
            [
                'cash_received' => $cashReceived,
                'cash_change' => $result['pembayaran']->cash_change,
                'paid_by' => Auth::id(),
            ]
        );

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()
            ->route('payment.success', $result['transaksi']->id_parkir)
            ->with('success', 'Pembayaran tunai dikonfirmasi.');
    }

    /**
     * Batalkan intent tunai pending (misal pelanggan ganti ke Midtrans).
     */
    public function cancelPending(Request $request, int $id_pembayaran): RedirectResponse|JsonResponse
    {
        $this->ensureStaff();

        $pembayaran = Pembayaran::query()->findOrFail($id_pembayaran);

        if ($pembayaran->metode !== self::METODE_CASH || $pembayaran->status !== 'pending') {
            throw ValidationException::withMessages([
                'id_pembayaran' => ['Hanya pembayaran tunai pending yang dapat dibatalkan.'],
            ]);
        }

        if ($pembayaran->id_kas_shift) {
            $shift = KasShift::query()->find($pembayaran->id_kas_shift);
            if ($shift && $shift->isClosed()) {
                throw ValidationException::withMessages([
                    'shift' => ['Shift tertutup; tidak dapat membatalkan.'],
                ]);
            }
        }

        $pembayaran->update([
            'status' => 'gagal',
            'keterangan' => 'Pembayaran tunai dibatalkan',
        ]);

        $this->logActivity(
            "Batal intent tunai #{$id_pembayaran}",
            'pembayaran',
            $pembayaran,
            []
        );

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Intent tunai dibatalkan.');
    }

    /**
     * Rekap harian per metode (hanya pembayaran berhasil).
     */
    public function dailyBreakdown(Request $request): JsonResponse
    {
        $this->ensureStaff();

        $request->validate([
            'tanggal' => 'nullable|date',
        ]);

        $date = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->toDateString()
            : now()->toDateString();

        $rows = Pembayaran::query()
            ->where('status', 'berhasil')
            ->whereDate('waktu_pembayaran', $date)
            ->selectRaw('LOWER(TRIM(metode)) as metode_norm, SUM(nominal) as total, COUNT(*) as jumlah')
            ->groupBy(DB::raw('LOWER(TRIM(metode))'))
            ->get()
            ->keyBy('metode_norm');

        $cash = (float) ($rows->get('cash')->total ?? 0);
        $midtrans = (float) ($rows->get('midtrans')->total ?? 0);
        $nestonpay = (float) ($rows->get('nestonpay')->total ?? 0);

        return response()->json([
            'tanggal' => $date,
            'total_cash' => $cash,
            'total_midtrans' => $midtrans,
            'total_nestonpay' => $nestonpay,
            'per_metode' => $rows->map(fn ($r) => [
                'total' => (float) $r->total,
                'jumlah' => (int) $r->jumlah,
            ]),
        ]);
    }

    private function ensureStaffCanAccessTransaksi(Transaksi $transaksi): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (in_array($user->role, ['admin', 'owner'], true)) {
            return;
        }

        if ($user->role === 'petugas') {
            return;
        }

        if ((int) $transaksi->id_user === (int) $user->id) {
            return;
        }

        abort(403);
    }
}
