<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\User;
use App\Models\AreaParkir;
use App\Models\ParkingMapSlot;
use App\Models\ParkingSlotReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Traits\LogsActivity;
use App\Support\PlatNomorNormalizer;

class TransaksiController extends Controller
{
    use LogsActivity;

    // == ALUR UTAMA PARKIR ==

    /**
     * Menangani proses kendaraan masuk (check-in).
     * Input manual plat + tarif + area cukup; scan kamera opsional.
     * Plat dicocokkan di server (normalisasi sama seperti API) agar tetap jalan walau JS/field tersembunyi bermasalah.
     */
    public function checkIn(Request $request)
    {
        // Debug: Log request data
        \Illuminate\Support\Facades\Log::info('CheckIn Request:', $request->all());

        $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'id_tarif' => 'required|exists:tb_tarif,id_tarif',
            'id_area' => 'required|exists:tb_area_parkir,id_area',
            'parking_map_slot_id' => 'nullable|integer|exists:tb_parking_map_slots,id',
        ], [
            'plat_nomor.required' => 'Plat nomor wajib diisi.',
            'id_tarif.required' => 'Tarif wajib dipilih.',
            'id_area.required' => 'Area parkir wajib dipilih.',
        ]);

        $platNormalized = PlatNomorNormalizer::normalize($request->plat_nomor);

        try {
            $transaksi = DB::transaction(function () use ($request, $platNormalized) {
                $area = AreaParkir::lockForUpdate()->findOrFail($request->id_area);

                if ($area->terisi >= $area->kapasitas) {
                    throw new \Exception('Kapasitas area ' . $area->nama_area . ' sudah penuh.');
                }

                // Lock baris kendaraan agar request paralel untuk plat yang sama terserialize.
                // (Setelah unique index diterapkan, plat_nomor adalah canonical format).
                $kendaraan = Kendaraan::where('plat_nomor', $platNormalized)
                    ->lockForUpdate()
                    ->first();

                if (! $kendaraan) {
                    $tarif = Tarif::findOrFail($request->id_tarif);
                    $jenis = $request->filled('jenis_kendaraan')
                        ? $request->jenis_kendaraan
                        : $tarif->jenis_kendaraan;

                    if (empty($jenis)) {
                        throw new \Exception('Jenis kendaraan tidak valid. Silakan pilih jenis kendaraan.');
                    }

                    try {
                        $kendaraan = Kendaraan::create([
                            'plat_nomor' => $platNormalized,
                            'jenis_kendaraan' => $jenis,
                            'warna' => $request->warna,
                            'pemilik' => $request->pemilik,
                            'id_user' => Auth::id(),
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Kemungkinan unique key violation: ambil record yang sudah dibuat request paralel.
                        $kendaraan = Kendaraan::where('plat_nomor', $platNormalized)
                            ->lockForUpdate()
                            ->firstOrFail();
                    }
                }

                $id_kendaraan = (int) $kendaraan->id_kendaraan;

                // Double-entry prevention: kendaraan tidak boleh punya transaksi status masuk yang aktif.
                $activeTransaksi = Transaksi::where('id_kendaraan', $id_kendaraan)
                    ->whereNull('waktu_keluar')
                    ->where('status', 'masuk')
                    ->lockForUpdate()
                    ->first();

                if ($activeTransaksi) {
                    throw new \Exception(
                        'Kendaraan dengan plat ' . $request->plat_nomor . ' sudah tercatat masuk dan belum keluar.'
                    );
                }

                $now = \Illuminate\Support\Carbon::now();
                $slotId = $request->filled('parking_map_slot_id') ? (int) $request->parking_map_slot_id : null;

                if ($slotId) {
                    $slot = \App\Models\ParkingMapSlot::lockForUpdate()
                        ->findOrFail($slotId);

                    if ((int) $slot->effectiveAreaId() !== (int) $request->id_area) {
                        throw new \Exception('Slot parkir ' . $slot->code . ' tidak tersedia untuk area yang dipilih.');
                    }

                    $tenMinutesAgo = Carbon::now()->subMinutes(10);
                    $occupied = Transaksi::where('parking_map_slot_id', $slot->id)
                        ->where(function ($q) use ($tenMinutesAgo) {
                            $q->where(function ($q2) {
                                $q2->whereNull('waktu_keluar')->where('status', 'masuk');
                            })->orWhere(function ($q2) use ($tenMinutesAgo) {
                                $q2->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
                            });
                        })
                        ->exists();

                    if ($occupied) {
                        throw new \Exception('Slot ' . $slot->code . ' sudah terisi.');
                    }

                    $reservation = \App\Models\ParkingSlotReservation::active()
                        ->where('parking_map_slot_id', $slot->id)
                        ->lockForUpdate()
                        ->first();

                    if ($reservation) {
                        if ((int) $reservation->id_kendaraan !== (int) $id_kendaraan) {
                            throw new \Exception('Slot ' . $slot->code . ' sedang dibooking.');
                        }
                        $reservation->delete();
                    }
                }

                // Buat transaksi
                $transaksi = Transaksi::create([
                    'id_kendaraan' => $id_kendaraan,
                    'id_tarif' => $request->id_tarif,
                    'id_area' => $request->id_area,
                    'parking_map_slot_id' => $slotId,
                    'id_user' => Auth::id(),
                    'waktu_masuk' => $now,
                    'status' => 'masuk',
                    'catatan' => $request->catatan,
                ]);

                $area->increment('terisi');

                $this->logActivity(
                    "Kendaraan {$request->plat_nomor} masuk ke area {$area->nama_area}",
                    'transaksi',
                    $transaksi,
                    ['plat_nomor' => $request->plat_nomor, 'area' => $area->nama_area]
                );

                return $transaksi;
            });

            return redirect()->route('transaksi.parkir.index')
                ->with('success', 'Berhasil! Kendaraan ' . $request->plat_nomor . ' tercatat masuk.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menangani proses kendaraan keluar (check-out).
     * Menggunakan transaction untuk atomic operation
     */
    public function checkOut(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transaksi = Transaksi::lockForUpdate()->findOrFail($id);

                // Validasi status
                if ($transaksi->status === 'keluar') {
                    throw new \Exception('Kendaraan ini sudah tercatat keluar.');
                }

                // Kalkulasi durasi dan biaya
                $waktu_keluar = \Illuminate\Support\Carbon::now();
                $tarif = $transaksi->tarif;

                if (! $tarif) {
                    throw new \Exception('Tarif tidak ditemukan untuk transaksi ini.');
                }

                $durasi_menit = abs($waktu_keluar->diffInMinutes($transaksi->waktu_masuk));
                $durasi_jam = (int) ceil($durasi_menit / 60);

                // Minimal 1 jam
                if ($durasi_jam < 1) {
                    $durasi_jam = 1;
                }

                $biaya_total = $durasi_jam * $tarif->tarif_perjam;

                // Diskon 10% jika user memiliki rfid_uid (kartu member)
                if ($transaksi->id_user) {
                    $user = $transaksi->user ?: User::find($transaksi->id_user);
                    if ($user && !empty($user->rfid_uid)) {
                        $biaya_total = $biaya_total * 0.9;
                    }
                }

                // Update transaksi
                $transaksi->update([
                    'waktu_keluar' => $waktu_keluar,
                    'durasi_jam' => $durasi_jam,
                    'biaya_total' => $biaya_total,
                    'status' => 'keluar',
                    'status_pembayaran' => 'pending', // Status default saat checkout
                ]);

                // Decrement kapasitas area parkir dengan lock
                $area = AreaParkir::lockForUpdate()->findOrFail($transaksi->id_area);
                if ($area->terisi > 0) {
                    $area->decrement('terisi');
                }

                $this->logActivity(
                    "Kendaraan {$transaksi->kendaraan->plat_nomor} checkout dari area {$area->nama_area}",
                    'transaksi',
                    $transaksi,
                    [
                        'plat_nomor' => $transaksi->kendaraan->plat_nomor,
                        'area' => $area->nama_area,
                        'biaya' => $biaya_total
                    ]
                );
            });

            return redirect()->route('payment.select-transaction')
                ->with('success', 'Kendaraan berhasil checkout. Silakan lanjut ke pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal checkout: ' . $e->getMessage());
        }
    }

    // == CRUD UNTUK ADMIN ==

    /**
     * Tampilan utama manajemen parkir (Petugas & Admin).
     * Menyatukan Parkir Aktif, Booking, dan Riwayat dalam satu halaman dengan tab.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'aktif'); // default ke parkir aktif
        $tenMinutesAgo = Carbon::now()->subMinutes(10);

        // Cleanup expired bookmarks
        Transaksi::where('status', 'bookmarked')
            ->where('bookmarked_at', '<', $tenMinutesAgo)
            ->delete();

        $query = Transaksi::with(['kendaraan', 'tarif', 'user', 'area', 'parkingMapSlot']);

        // Filter berdasarkan status/tab
        if ($status === 'aktif') {
            $query->where('status', 'masuk')->whereNull('waktu_keluar');
        } elseif ($status === 'booking') {
            $query->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
        } elseif ($status === 'riwayat') {
            $query->where('status', 'keluar')->where('status_pembayaran', 'berhasil');
        }

        // Pencarian plat nomor
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('kendaraan', function($sub) use ($search) {
                $sub->where('plat_nomor', 'like', "%{$search}%");
            });
        }

        // Filter Area
        if ($request->filled('area')) {
            $query->where('id_area', $request->area);
        }

        // Filter Tanggal (hanya untuk riwayat)
        if ($status === 'riwayat') {
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('waktu_keluar', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('waktu_keluar', '<=', $request->tanggal_sampai);
            }
        }

        $items = $query->orderBy($status === 'riwayat' ? 'waktu_keluar' : 'created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $areas = AreaParkir::orderBy('nama_area')->get();
        $title = 'Management Parkir';

        // Hitung count untuk badges di tab
        $counts = [
            'aktif' => Transaksi::where('status', 'masuk')->whereNull('waktu_keluar')->count(),
            'booking' => Transaksi::where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo)->count(),
        ];

        return view('transaksi.index', [
            'transaksis' => $items,
            'areas' => $areas,
            'title' => $title,
            'currentStatus' => $status,
            'counts' => $counts
        ]);
    }

    /**
     * @deprecated Gunakan index() dengan parameter status=aktif
     */
    public function activeParking(Request $request)
    {
        return redirect()->route('transaksi.index', ['status' => 'aktif']);
    }

    /**
     * @deprecated Gunakan index() dengan parameter status=booking
     */
    public function bookings(Request $request)
    {
        return redirect()->route('transaksi.index', ['status' => 'booking']);
    }

    /**
     * @deprecated Gunakan index() dengan parameter status=riwayat
     */
    public function history(Request $request)
    {
        return redirect()->route('transaksi.index', ['status' => 'riwayat']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kendaraan' => 'required|exists:tb_kendaraan,id_kendaraan',
            'waktu_masuk' => 'required|date',
            'id_tarif' => 'required|exists:tb_tarif,id_tarif',
            'id_user' => 'required|exists:tb_user,id',
            'id_area' => 'required|exists:tb_area_parkir,id_area',
            'status' => 'required|in:masuk,keluar',
            'catatan' => 'nullable|string|max:255',
        ]);

        $transaksi = Transaksi::create($data);
        $this->logActivity(
            "Membuat transaksi manual (ID: {$transaksi->id_transaksi})",
            'transaksi',
            $transaksi,
            $data
        );
        return redirect()->route('transaksi.index')->with('success','Transaksi manual berhasil dibuat');
    }

    public function create()
    {
        $kendaraans = Kendaraan::orderBy('plat_nomor')->get();
        $tarifs = Tarif::orderBy('jenis_kendaraan')->get();
        $areas = AreaParkir::orderBy('nama_area')->get();
        $cameras = \App\Models\Camera::scanner()->orderBy('is_default', 'desc')->orderBy('id')->get();

        /** @var \Illuminate\Support\Collection<int, array{id:int, code:string, id_area:int, occupied:bool}> $slots */
        $slots = $this->buildCheckInSlotsPayload();

        return view('parkir.create', compact('kendaraans', 'tarifs', 'areas', 'cameras', 'slots'));
    }

    /**
     * Data slot untuk form check-in: satu sumber kebenaran dengan validasi checkIn()
     * (masuk aktif + bookmark + reservasi aktif = tidak bisa dipilih).
     */
    private function buildCheckInSlotsPayload()
    {
        $models = ParkingMapSlot::query()
            ->whereNotNull('area_parkir_id')
            ->orderBy('code')
            ->get();

        $slotIds = $models->pluck('id')->all();
        $blockedSlotIds = [];

        if (!empty($slotIds)) {
            $tenMinutesAgo = Carbon::now()->subMinutes(10);
            $txBlocked = Transaksi::whereIn('parking_map_slot_id', $slotIds)
                ->whereNotNull('parking_map_slot_id')
                ->where(function ($q) use ($tenMinutesAgo) {
                    $q->where(function ($q2) {
                        $q2->whereNull('waktu_keluar')->where('status', 'masuk');
                    })->orWhere(function ($q2) use ($tenMinutesAgo) {
                        $q2->where('status', 'bookmarked')->where('bookmarked_at', '>', $tenMinutesAgo);
                    });
                })
                ->pluck('parking_map_slot_id')
                ->all();

            $rsvBlocked = ParkingSlotReservation::active()
                ->whereIn('parking_map_slot_id', $slotIds)
                ->pluck('parking_map_slot_id')
                ->all();

            $blockedSlotIds = array_values(array_unique(array_merge($txBlocked, $rsvBlocked)));
        }

        return $models->map(function (ParkingMapSlot $s) use ($blockedSlotIds) {
            return [
                'id' => (int) $s->id,
                'code' => (string) $s->code,
                'id_area' => (int) $s->area_parkir_id,
                'occupied' => in_array($s->id, $blockedSlotIds, true),
            ];
        })->values();
    }

    public function show($id)
    {
        $item = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])->findOrFail($id);
        return view('transaksi.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Transaksi::findOrFail($id);
        $kendaraans = Kendaraan::orderBy('plat_nomor')->get();
        $tarifs = Tarif::orderBy('jenis_kendaraan')->get();
        $users = User::orderBy('name')->get();
        $areas = AreaParkir::orderBy('nama_area')->get();
        return view('transaksi.edit', compact('item','kendaraans','tarifs','users','areas'));
    }

    public function update(Request $request, $id)
    {
        $item = Transaksi::findOrFail($id);
        $data = $request->validate([
            'waktu_keluar' => 'nullable|date',
            'durasi_jam' => 'nullable|integer',
            'biaya_total' => 'nullable|numeric',
            'status' => 'required|in:masuk,keluar',
        ]);

        $item->update($data);
        $this->logActivity(
            "Mengupdate transaksi manual (ID: {$item->id_transaksi})",
            'transaksi',
            $item,
            $data
        );
        return redirect()->route('transaksi.index')->with('success','Transaksi manual berhasil diupdate');
    }

    public function print($id)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'user', 'area'])->findOrFail($id);
        return view('parkir.receipt', compact('transaksi'));
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $this->logActivity(
            "Menghapus transaksi (ID: {$transaksi->id_transaksi})",
            'transaksi',
            $transaksi,
            $transaksi->toArray()
        );
        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success','Transaksi berhasil dihapus');
    }

    /**
     * Petugas menyetujui reservasi user (user sudah datang).
     */
    public function acceptReservation($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'bookmarked') {
            return redirect()->back()->with('error', 'Transaksi ini bukan merupakan reservasi.');
        }

        // Cek apakah sudah lewat 10 menit
        if ($transaksi->bookmarked_at->diffInMinutes(now()) > 10) {
            return redirect()->back()->with('error', 'Reservasi ini sudah kadaluwarsa (lebih dari 10 menit).');
        }

        $transaksi->update([
            'status' => 'masuk',
            'waktu_masuk' => now(),
        ]);

        $this->logActivity(
            "Menyetujui reservasi parkir (ID: {$transaksi->id_transaksi})",
            'transaksi',
            $transaksi
        );

        return redirect()->back()->with('success', 'Reservasi berhasil dikonfirmasi. Kendaraan resmi masuk.');
    }

    /**
     * Petugas menolak/membatalkan reservasi user.
     */
    public function rejectReservation($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'bookmarked') {
            return redirect()->back()->with('error', 'Transaksi ini bukan merupakan reservasi.');
        }

        $transaksi->delete();

        $this->logActivity(
            "Menolak/Membatalkan reservasi parkir (ID: {$transaksi->id_transaksi})",
            'transaksi',
            $transaksi
        );

        return redirect()->back()->with('success', 'Reservasi telah dibatalkan.');
    }

    /**
     * Normalisasi plat: huruf besar, hanya A–Z dan 0–9 (spasi/tanda diabaikan).
     */
    private function normalizePlatNomor(string $plat): string
    {
        // Backward compatibility: gunakan utilitas normalisasi terpusat.
        return PlatNomorNormalizer::normalize($plat);
    }

    private function findKendaraanByNormalizedPlat(string $normalized): ?Kendaraan
    {
        if ($normalized === '') {
            return null;
        }

        return Kendaraan::query()
            ->orderByDesc('id_kendaraan')
            ->get(['id_kendaraan', 'plat_nomor'])
            ->first(function (Kendaraan $k) use ($normalized) {
                return PlatNomorNormalizer::normalize((string) $k->plat_nomor) === $normalized;
            });
    }
}
