<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkirController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\PetugasDashboardController;
use App\Http\Controllers\ParkingSlotController;
use App\Http\Controllers\ParkingMapController;
use App\Http\Controllers\NfcAdminController;
use App\Http\Controllers\NfcController;
use App\Http\Controllers\RfidParkingController;
use App\Http\Controllers\RfidIdentifyController;
use App\Http\Controllers\RfidAccessController;
use App\Http\Controllers\RfidLoginController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

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
Route::get('/login', [LoginController::class, 'create'])->name('login.create');
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('throttle:5,1');
Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store')->middleware('throttle:5,1');
// Lupa password: belum implement; redirect ke login dengan pesan
Route::get('/forgot-password', function () {
    return redirect()->route('login.create')->with('info', 'Lupa password? Hubungi administrator untuk reset password.');
})->name('password.request');

// Protected Routes - Require Authentication
Route::middleware(['auth', 'no-cache'])->group(function () {
    // API & halaman Peta Parkir: admin, petugas, dan user (user hanya untuk lihat & booking slot)
    Route::middleware(['role:admin,petugas,user'])->group(function () {
        // Halaman peta parkir (Leaflet + image overlay)
        Route::get('/parking-map', [ParkingSlotController::class, 'view'])->name('parking.map.index');
        // API data slot + kamera + summary (untuk Leaflet)
        Route::get('/api/parking-slots', [ParkingSlotController::class, 'index'])->name('api.parking-slots');
        Route::get('/api/areas/{area}/slots', [ParkingSlotController::class, 'slotsByArea'])->name('api.areas.slots');

        // Endpoint lain untuk fitur bookmark lama tetap menggunakan ParkingMapController
        Route::post('/api/parking-slots/{area_id}/bookmark', [\App\Http\Controllers\Api\ParkingMapController::class, 'bookmark'])->name('api.parking-slots.bookmark');
        Route::post('/api/parking-slots/{id_transaksi}/unbookmark', [\App\Http\Controllers\Api\ParkingMapController::class, 'unbookmark'])->name('api.parking-slots.unbookmark');

        // Plate Recognizer API
        Route::post('/scan-plate', [\App\Http\Controllers\Api\PlateRecognizerController::class, 'scanPlate'])->name('api.scan-plate');

        // New ANPR Features
        Route::get('/anpr', function () {
            return view('anpr.index');
        })->name('anpr.index');
        Route::post('/api/anpr/scan', [\App\Http\Controllers\ANPRController::class, 'scan'])->name('api.anpr.scan');

        // Kendaraan search (autocomplete)
        Route::get('/api/kendaraan/search', [\App\Http\Controllers\Api\KendaraanSearchController::class, 'search'])->name('api.kendaraan.search');
        Route::get('/api/kendaraan/check-plat', [\App\Http\Controllers\Api\KendaraanSearchController::class, 'checkPlat'])->name('api.kendaraan.check-plat');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

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
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        } else {
            unset($data['password']);
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
    Route::get('/user/bookings', function (\Illuminate\Http\Request $request) {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $areas = \App\Models\AreaParkir::orderBy('nama_area')->get();
        $map = \App\Models\ParkingMap::getDefaultOrFirst();
        $kendaraans = \App\Models\Kendaraan::where('id_user', $user->id)->orderBy('plat_nomor')->get();
        $tarifs = \App\Models\Tarif::orderBy('jenis_kendaraan')->get();

        // Hitung status per area (kosong / terisi / dibookmark)
        $now = \Carbon\Carbon::now();
        $statusPerArea = [];
        $myBookingIds = [];

        foreach ($areas as $area) {
            $existing = \App\Models\Transaksi::where('id_area', $area->id_area)
                ->where(function ($q) use ($now) {
                    $q->where(function ($q2) {
                        $q2->whereNull('waktu_keluar')
                            ->where('status', 'masuk');
                    })->orWhere(function ($q2) use ($now) {
                        $q2->where('status', 'bookmarked')
                            ->where('bookmarked_at', '>', $now->copy()->subMinutes(10));
                    });
                })
                ->first();

            if (! $existing) {
                $statusPerArea[$area->id_area] = 'empty';
            } elseif ($existing->status === 'bookmarked' && $existing->id_user === $user->id) {
                $statusPerArea[$area->id_area] = 'bookmarked-by-me';
                $myBookingIds[$area->id_area] = $existing->id_parkir;
            } elseif ($existing->status === 'bookmarked') {
                $statusPerArea[$area->id_area] = 'bookmarked';
            } else {
                $statusPerArea[$area->id_area] = 'occupied';
            }
        }

        return view('user.bookings', compact('areas', 'statusPerArea', 'map', 'myBookingIds', 'kendaraans', 'tarifs'));
    })->name('user.bookings');

    Route::post('/user/bookings/areas/{area}', [\App\Http\Controllers\Api\ParkingMapController::class, 'bookmark'])
        ->name('user.bookings.book');

    Route::delete('/user/bookings/areas/{transaksi}', [\App\Http\Controllers\Api\ParkingMapController::class, 'unbookmark'])
        ->name('user.bookings.unbook');

    // User: Tagihan sendiri (transaksi keluar tapi belum dibayar)
    Route::get('/user/bills', [\App\Http\Controllers\PaymentController::class, 'userBills'])
        ->name('user.bills');

    // User: Saldo NestonPay
    Route::get('/user/saldo', [\App\Http\Controllers\SaldoController::class, 'index'])->name('user.saldo.index');
    Route::get('/user/saldo/topup', [\App\Http\Controllers\SaldoController::class, 'topup'])->name('user.saldo.topup');
    Route::post('/user/saldo/topup', [\App\Http\Controllers\SaldoController::class, 'storeTopupManual'])->name('user.saldo.topup.store');
    Route::post('/user/saldo/pay/{id_parkir}', [\App\Http\Controllers\SaldoController::class, 'processPayWithSaldo'])->name('user.saldo.pay');

    // ========== ADMIN ONLY (sesuai Tabel Fitur SPK) ==========
    // Admin: CRUD User, CRUD Tarif, CRUD Area Parkir, CRUD Kendaraan, CRUD Layout Peta, Akses Log Aktifitas, Cetak struk parkir
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::get('/admin/users/{id}/scan-rfid', [\App\Http\Controllers\UserController::class, 'showScanPage'])
            ->name('admin.users.scan-rfid');
        Route::post('/admin/users/{id}/save-rfid', [\App\Http\Controllers\UserController::class, 'saveRfid'])
            ->name('admin.users.save-rfid');
        Route::resource('area-parkir', \App\Http\Controllers\AreaParkirController::class);
        Route::post('area-parkir/{area}/create-layout', [\App\Http\Controllers\AreaParkirController::class, 'createLayout'])->name('area-parkir.create-layout');
        Route::resource('kendaraan', \App\Http\Controllers\KendaraanController::class);
        Route::resource('tarif', \App\Http\Controllers\TarifController::class);
        Route::resource('parking-maps', ParkingMapController::class);
        Route::get('parking-maps/{parking_map}/slots', [\App\Http\Controllers\ParkingMapSlotController::class, 'index'])->name('parking-maps.slots.index');
        Route::get('parking-maps/{parking_map}/slots/create', [\App\Http\Controllers\ParkingMapSlotController::class, 'create'])->name('parking-maps.slots.create');
        Route::post('parking-maps/{parking_map}/slots', [\App\Http\Controllers\ParkingMapSlotController::class, 'store'])->name('parking-maps.slots.store');
        Route::get('parking-maps/{parking_map}/slots/{slot}/edit', [\App\Http\Controllers\ParkingMapSlotController::class, 'edit'])->name('parking-maps.slots.edit');
        Route::put('parking-maps/{parking_map}/slots/{slot}', [\App\Http\Controllers\ParkingMapSlotController::class, 'update'])->name('parking-maps.slots.update');
        Route::delete('parking-maps/{parking_map}/slots/{slot}', [\App\Http\Controllers\ParkingMapSlotController::class, 'destroy'])->name('parking-maps.slots.destroy');
        Route::get('parking-maps/{parking_map}/cameras', [\App\Http\Controllers\ParkingMapCameraController::class, 'index'])->name('parking-maps.cameras.index');
        Route::post('parking-maps/{parking_map}/cameras', [\App\Http\Controllers\ParkingMapCameraController::class, 'store'])->name('parking-maps.cameras.store');
        Route::delete('parking-maps/{parking_map}/cameras/{map_camera}', [\App\Http\Controllers\ParkingMapCameraController::class, 'destroy'])->name('parking-maps.cameras.destroy');
        Route::resource('log-aktivitas', \App\Http\Controllers\LogAktifitasController::class);
        Route::resource('kamera', \App\Http\Controllers\CameraController::class);
        Route::get('/transaksi/{id}/print', [\App\Http\Controllers\TransaksiController::class, 'print'])->name('transaksi.print');
    });

    // Transaksi: index & show untuk Admin dan Petugas (lihat riwayat tanpa edit/hapus)
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::get('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/create-check-in', [\App\Http\Controllers\TransaksiController::class, 'create'])->name('transaksi.create-check-in');
        Route::post('/transaksi/check-in', [\App\Http\Controllers\TransaksiController::class, 'checkIn'])->name('transaksi.checkIn');
        Route::get('/transaksi/{transaksi}', [\App\Http\Controllers\TransaksiController::class, 'show'])
            ->whereNumber('transaksi')
            ->name('transaksi.show');
    });

    // Transaksi: CRUD hanya untuk Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/transaksi/create', [\App\Http\Controllers\TransaksiController::class, 'create'])->name('transaksi.create');
        Route::post('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/{transaksi}/edit', [\App\Http\Controllers\TransaksiController::class, 'edit'])->name('transaksi.edit');
        Route::put('/transaksi/{transaksi}', [\App\Http\Controllers\TransaksiController::class, 'update'])->name('transaksi.update');
        Route::delete('/transaksi/{transaksi}', [\App\Http\Controllers\TransaksiController::class, 'destroy'])->name('transaksi.destroy');
    });

    // ========== PETUGAS ONLY (sesuai SPK: Transaksi) ==========
    // Petugas: Transaksi (catat masuk, parkir aktif, checkout, pembayaran)
    Route::middleware(['role:petugas'])->group(function () {
        Route::get('/parkir-aktif', [\App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.parkir.index')->defaults('status', 'masuk');
        Route::put('/transaksi/{id}/check-out', [\App\Http\Controllers\TransaksiController::class, 'checkOut'])->name('transaksi.checkOut');
        Route::get('/payment/select-transaction', [\App\Http\Controllers\PaymentController::class, 'selectTransaction'])->name('payment.select-transaction');
        Route::get('/payment/{id_parkir}', [\App\Http\Controllers\PaymentController::class, 'create'])->name('payment.create');
        Route::get('/payment-history', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
    });

    // NFC: halaman scan (tampil untuk admin + petugas)
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::get('/nfc/scan', function () {
            return view('nfc.scan');
        })->name('nfc.scan');
    });

    // RFID: halaman scan operasional (tampil untuk admin + petugas)
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::get('/parkir/scan', [RfidParkingController::class, 'scanPage'])->name('parkir.scan');
        Route::post('/api/parkir/scan-rfid', [RfidParkingController::class, 'scan'])
            ->name('api.parkir.scan-rfid');
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

    // NFC: halaman write (admin saja)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/nfc/admin/write', [NfcAdminController::class, 'index'])->name('nfc.admin.write');
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
