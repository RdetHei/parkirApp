<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\PetugasDashboardController;
use App\Http\Controllers\ParkingSlotController;
use App\Http\Controllers\NfcController;
use App\Http\Controllers\RfidParkingController;
use App\Http\Controllers\RfidIdentifyController;
use App\Http\Controllers\RfidAccessController;
use App\Http\Controllers\RfidLoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RevenueReconciliationController;
use App\Support\UserPhoto;
use App\Http\Controllers\RfidAdminController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\LandingController;

Route::post('/kendaraan/upload', [KendaraanController::class, 'store']);

// Public Routes
Route::get('/', [LandingController::class, 'index']);

Route::get('/lang/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

Route::post('/api/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/docs', function () {
    return view('docs');
})->name('docs');

// RFID Login (for fun): scan kartu untuk Auth::login()
Route::get('/login/rfid', [RfidLoginController::class, 'page'])->name('rfid.login.page');
Route::post('/api/rfid/login', [RfidLoginController::class, 'login'])
    ->name('api.rfid.login')
    ->middleware('throttle:20,1');

// NFC APIs (buat Web NFC)
Route::post('/api/encrypt-id', [NfcController::class, 'encryptId'])
    ->middleware(['auth', 'role:admin']);
Route::post('/api/nfc-write', [NfcController::class, 'nfcWrite'])
    ->middleware(['auth', 'role:admin']);
Route::post('/api/nfc-scan', [NfcController::class, 'nfcScan']);
Route::post('/api/parkir/masuk', [NfcController::class, 'parkirMasuk']);
Route::post('/api/parkir/keluar', [NfcController::class, 'parkirKeluar']);


// Midtrans notification callbacks (public, rate-limited; verifikasi signature di controller)
Route::post('/payment/midtrans/notification', [\App\Http\Controllers\PaymentController::class, 'midtransNotification'])
    ->name('payment.midtrans.notification')
    ->middleware('throttle:60,1');

Route::post('/payment/midtrans/recurring-notification', [\App\Http\Controllers\PaymentController::class, 'midtransRecurringNotification'])
    ->name('payment.midtrans.recurring-notification')
    ->middleware('throttle:60,1');

Route::post('/payment/midtrans/pay-account-notification', [\App\Http\Controllers\PaymentController::class, 'midtransPayAccountNotification'])
    ->name('payment.midtrans.pay-account-notification')
    ->middleware('throttle:60,1');

// Midtrans redirect URLs (public)
Route::get('/payment/midtrans/{id_parkir}/finish', [\App\Http\Controllers\PaymentController::class, 'midtransFinish'])
    ->name('payment.midtrans.finish');

Route::get('/payment/midtrans/{id_parkir}/unfinish', [\App\Http\Controllers\PaymentController::class, 'midtransUnfinish'])
    ->name('payment.midtrans.unfinish');

Route::get('/payment/midtrans/{id_parkir}/error', [\App\Http\Controllers\PaymentController::class, 'midtransError'])
    ->name('payment.midtrans.error');

// Auth Routes
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('throttle:5,1');
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store')->middleware('throttle:5,1');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store')->middleware('throttle:5,1');
});

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::middleware(['auth', 'no-cache'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

// Protected: wajib email terverifikasi
Route::middleware(['auth', 'verified', 'no-cache'])->group(function () {
    // API & halaman Peta Parkir: admin, petugas, dan user
    Route::middleware(['role:admin,petugas,user'])->group(function () {
        // Plate Recognizer API
        Route::post('/scan-plate', [\App\Http\Controllers\Api\PlateRecognizerController::class, 'scanPlate'])->name('api.scan-plate');

        // Notification Check API
        Route::get('/api/notifications/check', function () {
            $user = request()->user();
            if (!$user) return response()->json(['has_new' => false]);

            $latest = \App\Models\NotificationLog::where('user_id', $user->id)
                ->where('created_at', '>', now()->subSeconds(30))
                ->where('status', 'success')
                ->latest()
                ->first();

            return response()->json([
                'has_new' => !!$latest,
                'message' => $latest ? $latest->message : null
            ]);
        })->name('api.notifications.check');

        // New ANPR Features
        Route::get('/anpr', function () {
            return view('anpr.index');
        })->name('anpr.index');
        Route::post('/api/anpr/scan', [\App\Http\Controllers\ANPRController::class, 'scan'])->name('api.anpr.scan');

        // Kendaraan search (autocomplete)
        Route::get('/api/kendaraan/search', [\App\Http\Controllers\Api\KendaraanSearchController::class, 'search'])->name('api.kendaraan.search');
        Route::get('/api/kendaraan/check-plat', [\App\Http\Controllers\Api\KendaraanSearchController::class, 'checkPlat'])->name('api.kendaraan.check-plat');
    });

    // Dashboard (admin only)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:admin');
    // Owner Dashboard
    Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard')->middleware('role:owner');

    // Petugas Dashboard
    Route::get('/petugas/dashboard', [PetugasDashboardController::class, 'index'])->name('petugas.dashboard')->middleware('role:petugas');

    // User Dashboard
    Route::get('/user/dashboard', function (\Illuminate\Http\Request $request) {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $totalKendaraan = \App\Models\Kendaraan::where('id_user', $user->id)->count();
        $totalTransaksi = \App\Models\Transaksi::where('id_user', $user->id)->count();
        $transaksiAktif = \App\Models\Transaksi::where('id_user', $user->id)
            ->where('status', 'masuk')
            ->count();

        $totalPembayaran = \App\Models\Pembayaran::where('id_user', $user->id)->count();
        $totalPengeluaran = \App\Models\Pembayaran::where('id_user', $user->id)
            ->where('status', 'berhasil')
            ->sum('nominal');

        // Tagihan (transaksi sudah keluar tapi belum dibayar)
        $transaksiBelumDibayar = \App\Models\Transaksi::where('id_user', $user->id)
            ->where('status', 'keluar')
            ->where(function ($q) {
                $q->whereNull('status_pembayaran')
                    ->orWhere('status_pembayaran', '!=', 'berhasil');
            })
            ->count();

        $riwayatTransaksi = \App\Models\Transaksi::with(['kendaraan', 'area', 'tarif'])
            ->where('id_user', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $riwayatPembayaran = \App\Models\Pembayaran::with(['transaksi'])
            ->where('id_user', $user->id)
            ->orderByDesc('waktu_pembayaran')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'user',
            'totalKendaraan',
            'totalTransaksi',
            'transaksiAktif',
            'totalPembayaran',
            'totalPengeluaran',
            'transaksiBelumDibayar',
            'riwayatTransaksi',
            'riwayatPembayaran'
        ));
    })->name('user.dashboard');

    // User: Riwayat transaksi lengkap
    Route::get('/user/history', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        $transactions = \App\Models\Transaksi::with(['kendaraan', 'area', 'tarif', 'pembayaran'])
            ->where('id_user', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);
        $title = 'Riwayat Parkir';
        return view('user.history', compact('transactions', 'title'));
    })->name('user.history');

    // User: Kelola profil sendiri
    Route::get('/user/profile', function (\Illuminate\Http\Request $request) {
        return view('user.profile', ['user' => $request->user(), 'title' => 'Profil Saya']);
    })->name('user.profile');

    Route::put('/user/profile', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tb_user,email,' . $user->id,
            'phone' => 'nullable|string|max:32',
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $photoFile = $request->file('photo');
        unset($data['photo']);

        if ($photoFile?->isValid()) {
            $data = array_merge($data, UserPhoto::replaceWithUpload($photoFile, $user));
        }

        $user->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    })->name('user.profile.update');

    // User: Kelola kendaraan milik sendiri
    Route::get('/user/vehicles', [\App\Http\Controllers\UserVehicleController::class, 'index'])->name('user.vehicles.index');
    Route::post('/user/vehicles', [\App\Http\Controllers\UserVehicleController::class, 'store'])->name('user.vehicles.store');
    Route::put('/user/vehicles/{vehicle}', [\App\Http\Controllers\UserVehicleController::class, 'update'])->name('user.vehicles.update');
    Route::delete('/user/vehicles/{vehicle}', [\App\Http\Controllers\UserVehicleController::class, 'destroy'])->name('user.vehicles.destroy');

    // User: Booking slot (area-based bookmark)
    Route::get('/user/bookings/areas/{area?}', function (\Illuminate\Http\Request $request, $areaId = null) {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $selectedDaerah = $request->query('daerah');
        $daerahs = \App\Models\AreaParkir::query()
            ->whereNotNull('daerah')
            ->where('daerah', '!=', '')
            ->orderBy('daerah')
            ->distinct()
            ->pluck('daerah');

        $query = \App\Models\AreaParkir::orderBy('nama_area');
        if ($selectedDaerah) {
            $query->where('daerah', $selectedDaerah);
        }
        $areas = $query->get();

        // Pilih peta area aktif berdasarkan filter + pilihan user.
        $map = null;
        if ($areaId) {
            $map = $areas->firstWhere('id_area', (int) $areaId) ?? \App\Models\AreaParkir::find((int) $areaId);
        }
        if (!$map) {
            $map = $areas->first() ?? \App\Models\AreaParkir::getDefaultMap();
        }
        if ($selectedDaerah && $map && !$areas->contains('id_area', $map->id_area)) {
            $map = $areas->first();
        }
        $selectedAreaId = $map?->id_area;

        $kendaraans = \App\Models\Kendaraan::where('id_user', $user->id)->orderBy('plat_nomor')->get();
        $tarifs = \App\Models\Tarif::orderBy('jenis_kendaraan')->get();
        $activeBookingInfo = null;

        // Hitung status per area (kosong / terisi / dibookmark)
        $now = \Carbon\Carbon::now();
        $statusPerArea = [];
        $myBookingIds = [];

        foreach ($areas as $area) {
            $occupied = \App\Models\Transaksi::where('id_area', $area->id_area)
                ->whereNull('waktu_keluar')
                ->where('status', 'masuk')
                ->exists();

            if ($occupied) {
                $statusPerArea[$area->id_area] = 'occupied';
                continue;
            }

            $reservation = \App\Models\ParkingSlotReservation::active()
                ->where('id_area', $area->id_area)
                ->orderByDesc('expires_at')
                ->first();

            if ($reservation) {
                if ((int) $reservation->id_user === (int) $user->id) {
                    $statusPerArea[$area->id_area] = 'bookmarked-by-me';
                    $myBookingIds[$area->id_area] = $reservation->id;
                } else {
                    $statusPerArea[$area->id_area] = 'bookmarked';
                }
                continue;
            }

            $legacy = \App\Models\Transaksi::where('id_area', $area->id_area)
                ->where('status', 'bookmarked')
                ->where('bookmarked_at', '>', $now->copy()->subMinutes(10))
                ->orderByDesc('bookmarked_at')
                ->first();

            if ($legacy) {
                if ((int) $legacy->id_user === (int) $user->id) {
                    $statusPerArea[$area->id_area] = 'bookmarked-by-me';
                    $myBookingIds[$area->id_area] = $legacy->id_parkir;
                } else {
                    $statusPerArea[$area->id_area] = 'bookmarked';
                }
                continue;
            }

            $statusPerArea[$area->id_area] = 'empty';
        }

        $activeReservation = \App\Models\ParkingSlotReservation::active()
            ->with(['area:id_area,nama_area', 'slot:id,code'])
            ->where('id_user', $user->id)
            ->latest('expires_at')
            ->first();

        if ($activeReservation) {
            $activeBookingInfo = [
                'id' => (int) $activeReservation->id,
                'kind' => 'reservation',
                'area_name' => $activeReservation->area?->nama_area ?? '-',
                'slot_code' => $activeReservation->slot?->code ?? '-',
                'expires_at' => optional($activeReservation->expires_at)->toIso8601String(),
            ];
        } else {
            $legacyBooking = \App\Models\Transaksi::where('id_user', $user->id)
                ->where('status', 'bookmarked')
                ->where('bookmarked_at', '>', $now->copy()->subMinutes(10))
                ->with(['area:id_area,nama_area', 'parkingMapSlot:id,code'])
                ->latest('bookmarked_at')
                ->first();

            if ($legacyBooking) {
                $activeBookingInfo = [
                    'id' => (int) $legacyBooking->id_parkir,
                    'kind' => 'transaksi',
                    'area_name' => $legacyBooking->area?->nama_area ?? '-',
                    'slot_code' => $legacyBooking->parkingMapSlot?->code ?? '-',
                    'expires_at' => optional($legacyBooking->bookmarked_at)->addMinutes(10)->toIso8601String(),
                ];
            }
        }

        return view('user.bookings', compact('areas', 'statusPerArea', 'map', 'myBookingIds', 'kendaraans', 'tarifs', 'daerahs', 'selectedDaerah', 'selectedAreaId', 'activeBookingInfo'));
    })->name('user.bookings');

    Route::post('/user/bookings/areas/{area}', [\App\Http\Controllers\ParkingSlotController::class, 'bookmark'])->name('user.bookings.book');
    Route::delete('/user/bookings/areas/{id}', [\App\Http\Controllers\ParkingSlotController::class, 'unbookmark'])->name('user.bookings.unbook');

    // User: Tagihan sendiri (transaksi keluar tapi belum dibayar)
    Route::get('/user/bills', [\App\Http\Controllers\PaymentController::class, 'userBills'])
        ->name('user.bills');

    // User: Saldo NestonPay
    Route::get('/user/saldo', [\App\Http\Controllers\SaldoController::class, 'index'])->name('user.saldo.index');
    Route::get('/user/saldo/topup', [\App\Http\Controllers\SaldoController::class, 'topup'])->name('user.saldo.topup');
    Route::post('/user/saldo/topup', [\App\Http\Controllers\SaldoController::class, 'storeTopupManual'])->name('user.saldo.topup.store');
    Route::post('/user/saldo/topup/token', [\App\Http\Controllers\SaldoController::class, 'midtransSnapToken'])->name('user.saldo.topup.token');
    Route::post('/user/saldo/pay/{id_parkir}', [\App\Http\Controllers\SaldoController::class, 'processPayWithSaldo'])->name('user.saldo.pay');

    // ========== ADMIN ONLY (sesuai Tabel Fitur SPK) ==========
    // Admin: CRUD User, CRUD Tarif, CRUD Area Parkir, CRUD Kendaraan, CRUD Layout Peta, Akses Log Aktifitas, Cetak struk parkir
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/reconciliation/revenue', [RevenueReconciliationController::class, 'index'])->name('admin.reconciliation.revenue');
        Route::post('/admin/reconciliation/revenue/sync', [RevenueReconciliationController::class, 'syncMissingPayments'])->name('admin.reconciliation.revenue.sync');

        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::post('/users/{id}/topup', [\App\Http\Controllers\UserController::class, 'topup'])->name('users.topup');

        // Unified Area & Map Management
        Route::resource('area-parkir', \App\Http\Controllers\AreaParkirController::class);
        Route::get('area-parkir/{area}/design', [\App\Http\Controllers\AreaParkirController::class, 'design'])->name('area-parkir.design');
        Route::post('area-parkir/{area}/design', [\App\Http\Controllers\AreaParkirController::class, 'saveDesign'])->name('area-parkir.save-design');

        Route::resource('kendaraan', \App\Http\Controllers\KendaraanController::class);
        Route::resource('tarif', \App\Http\Controllers\TarifController::class);
        Route::resource('log-aktivitas', \App\Http\Controllers\LogAktifitasController::class);
        Route::resource('kamera', \App\Http\Controllers\CameraController::class);

        // User RFID Registration (Admin Only)
        Route::get('/users/{id}/scan-rfid', [UserController::class, 'showScanPage'])->name('users.scan-rfid');
        Route::post('/users/{id}/save-rfid', [UserController::class, 'saveRfid'])->name('users.save-rfid');

        // RFID Management (Admin Only)
        Route::get('/admin/rfid', [RfidAdminController::class, 'index'])->name('admin.rfid.index');
        Route::post('/admin/rfid', [RfidAdminController::class, 'store'])->name('admin.rfid.store');
        Route::delete('/admin/rfid/{id}/unlink', [RfidAdminController::class, 'unlink'])->name('admin.rfid.unlink');
    });

    // Peta Parkir: Visualisasi Real-time (Public for all logged in)
    Route::middleware(['role:admin,petugas,user'])->group(function () {
        Route::get('/parking-map', [\App\Http\Controllers\ParkingSlotController::class, 'view'])->name('parking.map.index');
        Route::get('/api/parking-map/data', [\App\Http\Controllers\ParkingSlotController::class, 'index'])->name('api.parking.map.data');
    });

    // ========== OPERATIONS (Admin & Petugas) ==========
    Route::middleware(['role:admin,petugas'])->group(function () {
        // Management Unifikasi
        Route::get('/active-parking', [\App\Http\Controllers\TransaksiController::class, 'activeParking'])->name('transaksi.active');
        Route::get('/active-bookings', [\App\Http\Controllers\TransaksiController::class, 'bookings'])->name('transaksi.bookings');
        Route::get('/parking-history', [\App\Http\Controllers\TransaksiController::class, 'history'])->name('transaksi.history');

        // Aliases / Compatibility
        Route::get('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'history'])->name('transaksi.index');
        Route::get('/parkir-aktif', [\App\Http\Controllers\TransaksiController::class, 'activeParking'])->name('transaksi.parkir.index');

        // Check-in & Check-out
        Route::get('/check-in', [\App\Http\Controllers\TransaksiController::class, 'create'])->name('transaksi.create-check-in');
        Route::post('/check-in', [\App\Http\Controllers\TransaksiController::class, 'checkIn'])->name('transaksi.checkIn');
        Route::put('/transaksi/{id}/check-out', [\App\Http\Controllers\TransaksiController::class, 'checkOut'])->name('transaksi.checkOut');

        // Details & Printing
        Route::get('/transaksi/{id}/print', [\App\Http\Controllers\TransaksiController::class, 'print'])->name('transaksi.print');
        Route::get('/transaksi/{transaksi}', [\App\Http\Controllers\TransaksiController::class, 'show'])->whereNumber('transaksi')->name('transaksi.show');

        // Reservasi: Accept & Reject
        Route::post('/transaksi/{transaksi}/accept-reservation', [\App\Http\Controllers\TransaksiController::class, 'acceptReservation'])->name('transaksi.accept-reservation');
        Route::post('/transaksi/{transaksi}/reject-reservation', [\App\Http\Controllers\TransaksiController::class, 'rejectReservation'])->name('transaksi.reject-reservation');

        // RFID Parking Operation
        Route::get('/parkir/scan', [RfidParkingController::class, 'index'])->name('parkir.scan');
        Route::post('/api/parkir/rfid-scan', [RfidParkingController::class, 'processScan'])->name('api.parkir.rfid-scan')->middleware('throttle:40,1');
        Route::get('/rfid/history', [RfidParkingController::class, 'history'])->name('rfid.history');

        // Kamera: daftar perangkat (read-only untuk petugas)
        Route::get('/petugas/kamera', [\App\Http\Controllers\CameraController::class, 'index'])->name('petugas.kamera.index');

        // Payments
        Route::get('/payment/select-transaction', [\App\Http\Controllers\PaymentController::class, 'selectTransaction'])->name('payment.select-transaction');
        Route::get('/payment/{id_parkir}', [\App\Http\Controllers\PaymentController::class, 'create'])->name('payment.create');
        Route::get('/payment-history', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
    });

    // Transaksi: CRUD Full hanya untuk Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/transaksi/create', [\App\Http\Controllers\TransaksiController::class, 'create'])->name('transaksi.create');
        Route::post('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/{transaksi}/edit', [\App\Http\Controllers\TransaksiController::class, 'edit'])->name('transaksi.edit');
        Route::put('/transaksi/{transaksi}', [\App\Http\Controllers\TransaksiController::class, 'update'])->name('transaksi.update');
        Route::delete('/transaksi/{transaksi}', [\App\Http\Controllers\TransaksiController::class, 'destroy'])->name('transaksi.destroy');
    });

    // RFID: identifikasi instan (tanpa transaksi) + kontrol akses berbasis kartu
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::get('/rfid/identify', [RfidIdentifyController::class, 'page'])->name('rfid.identify.page');
        Route::post('/api/rfid/identify', [RfidIdentifyController::class, 'identify'])->name('api.rfid.identify');

        Route::get('/rfid/access/scan', [RfidAccessController::class, 'scanPage'])->name('rfid.access.scan-page');
        Route::post('/api/rfid/access/scan', [RfidAccessController::class, 'scan'])->name('api.rfid.access.scan');
        Route::post('/rfid/access/clear', [RfidAccessController::class, 'clear'])->name('rfid.access.clear');

        // Contoh proteksi fitur dengan kartu (scan dulu baru boleh akses)
        Route::get('/rfid/access-demo', function () {
            return view('rfid.access-demo', ['title' => 'RFID Access Demo']);
        })->middleware('rfid.access');

        // Contoh proteksi berbasis role user kartu (sesuaikan role yang Anda pakai)
        Route::get('/rfid/access-demo-admin', function () {
            return view('rfid.access-demo', ['title' => 'RFID Access Demo (Admin Card)']);
        })->middleware('rfid.access:admin');
    });

    // Pembayaran Midtrans & struk: bisa diakses user (hanya untuk transaksi miliknya) dan petugas
    Route::get('/payment/{id_parkir}/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/{id_parkir}/midtrans', [\App\Http\Controllers\PaymentController::class, 'midtransPay'])->name('payment.midtrans');
    Route::post('/payment/{id_parkir}/midtrans/token', [\App\Http\Controllers\PaymentController::class, 'midtransSnapToken'])->name('payment.midtrans.token');

    // ========== OWNER ONLY (sesuai SPK: Rekap transaksi sesuai waktu) ==========
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/report/pembayaran', [\App\Http\Controllers\ReportController::class, 'pembayaran'])->name('report.pembayaran');
        Route::get('/report/transaksi', [\App\Http\Controllers\ReportController::class, 'transaksi'])->name('report.transaksi');
        Route::get('/report/pembayaran/export/csv', [\App\Http\Controllers\ReportController::class, 'exportPembayaranCSV'])->name('report.pembayaran.export-csv');
        Route::get('/report/transaksi/export/csv', [\App\Http\Controllers\ReportController::class, 'exportTransaksiCSV'])->name('report.transaksi.export-csv');
    });
});
