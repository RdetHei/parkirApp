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
use App\Http\Controllers\CashPaymentController;
use App\Http\Controllers\IpWebcamProxyController;

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

        // New ANPR Features
        Route::get('/anpr', [\App\Http\Controllers\CameraController::class, 'monitor'])->name('anpr.index');
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

    // Sesi area tugas (kode peta): admin & petugas
    Route::post('/operational-area', [PetugasDashboardController::class, 'setOperationalArea'])
        ->middleware('role:admin,petugas')
        ->name('operational-area.set');
    Route::post('/operational-area/clear', [PetugasDashboardController::class, 'clearOperationalArea'])
        ->middleware('role:admin,petugas')
        ->name('operational-area.clear');

    // User Dashboard
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    // User: Riwayat transaksi lengkap
    Route::get('/user/history', [UserController::class, 'history'])->name('user.history');

    // User: Kelola profil sendiri
    Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/user/profile', [UserController::class, 'profileUpdate'])->name('user.profile.update');

    // User: Kelola kendaraan milik sendiri
    Route::get('/user/vehicles', [\App\Http\Controllers\UserVehicleController::class, 'index'])->name('user.vehicles.index');
    Route::post('/user/vehicles', [\App\Http\Controllers\UserVehicleController::class, 'store'])->name('user.vehicles.store');
    Route::put('/user/vehicles/{vehicle}', [\App\Http\Controllers\UserVehicleController::class, 'update'])->name('user.vehicles.update');
    Route::delete('/user/vehicles/{vehicle}', [\App\Http\Controllers\UserVehicleController::class, 'destroy'])->name('user.vehicles.destroy');

    // User: Booking slot (area-based bookmark)
    Route::get('/user/bookings/areas/{area?}', [ParkingSlotController::class, 'bookingPage'])->name('user.bookings');


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
        Route::delete('log-aktivitas/delete-all', [\App\Http\Controllers\LogAktifitasController::class, 'deleteAll'])->name('log-aktivitas.deleteAll');
        Route::resource('log-aktivitas', \App\Http\Controllers\LogAktifitasController::class);
        Route::resource('kamera', \App\Http\Controllers\CameraController::class);

        // RFID Management (Admin Only) - RFID now bound to vehicles
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
        Route::get('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/active-parking', [\App\Http\Controllers\TransaksiController::class, 'activeParking'])->name('transaksi.active');
        Route::get('/active-bookings', [\App\Http\Controllers\TransaksiController::class, 'bookings'])->name('transaksi.bookings');
        Route::get('/parking-history', [\App\Http\Controllers\TransaksiController::class, 'history'])->name('transaksi.history');

        // Aliases / Compatibility
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
        Route::delete('/rfid/history/{id}', [RfidParkingController::class, 'destroyHistory'])->name('rfid.history.destroy');

        // Kamera: daftar perangkat (read-only untuk petugas)
        Route::get('/petugas/kamera', [\App\Http\Controllers\CameraController::class, 'index'])->name('petugas.kamera.index');

        // Payments
        Route::get('/payment/select-transaction', [\App\Http\Controllers\PaymentController::class, 'selectTransaction'])->name('payment.select-transaction');
        Route::get('/payment/{id_parkir}', [\App\Http\Controllers\PaymentController::class, 'create'])->name('payment.create');
        Route::get('/payment-history', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');

        // IP Webcam snapshot proxy (avoid tainted canvas on capture)
        Route::get('/proxy/ipwebcam/snapshot', [IpWebcamProxyController::class, 'snapshot'])
            ->name('proxy.ipwebcam.snapshot');
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

    // Pembayaran tunai + shift kas (petugas/admin)
    Route::middleware(['role:admin,petugas'])->prefix('kas')->name('kas.')->group(function () {
        Route::post('/shift/open', [CashPaymentController::class, 'openShift'])->name('shift.open');
        Route::post('/shift/{id_kas_shift}/close', [CashPaymentController::class, 'closeShift'])->name('shift.close');
        Route::post('/transaksi/{id_parkir}/cash/intent', [CashPaymentController::class, 'initiate'])->name('cash.intent');
        Route::post('/cash/confirm', [CashPaymentController::class, 'confirm'])->name('cash.confirm');
        Route::post('/cash/{id_pembayaran}/cancel', [CashPaymentController::class, 'cancelPending'])->name('cash.cancel');
        Route::get('/report/harian', [CashPaymentController::class, 'dailyBreakdown'])->name('report.harian');
    });

    // ========== OWNER ONLY (sesuai SPK: Rekap transaksi sesuai waktu) ==========
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/report/pembayaran', [\App\Http\Controllers\ReportController::class, 'pembayaran'])->name('report.pembayaran');
        Route::get('/report/transaksi', [\App\Http\Controllers\ReportController::class, 'transaksi'])->name('report.transaksi');
        Route::get('/report/pembayaran/export/csv', [\App\Http\Controllers\ReportController::class, 'exportPembayaranCSV'])->name('report.pembayaran.export-csv');
        Route::get('/report/transaksi/export/csv', [\App\Http\Controllers\ReportController::class, 'exportTransaksiCSV'])->name('report.transaksi.export-csv');
    });
});
