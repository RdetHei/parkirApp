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

// Public Routes
Route::get('/', function () {
    return view('welcome');
});


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
Route::middleware(['auth'])->group(function () {
    // API & halaman Peta Parkir: hanya admin dan petugas (sesuai kebutuhan operasional parkir)
    Route::middleware(['role:admin,petugas'])->group(function () {
        // Halaman peta parkir (Leaflet + image overlay)
        Route::get('/parking-map', [ParkingSlotController::class, 'view'])->name('parking.map.index');

        // Endpoint lain untuk fitur bookmark lama tetap menggunakan ParkingMapController
        Route::post('/api/parking-slots/{area_id}/bookmark', [\App\Http\Controllers\Api\ParkingMapController::class, 'bookmark'])->name('api.parking-slots.bookmark');
        Route::post('/api/parking-slots/{id_transaksi}/unbookmark', [\App\Http\Controllers\Api\ParkingMapController::class, 'unbookmark'])->name('api.parking-slots.unbookmark');

        // Plate Recognizer API
        Route::post('/scan-plate', [\App\Http\Controllers\Api\PlateRecognizerController::class, 'scanPlate'])->name('api.scan-plate');

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
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');

    // ========== ADMIN ONLY (sesuai Tabel Fitur SPK) ==========
    // Admin: CRUD User, CRUD Tarif, CRUD Area Parkir, CRUD Kendaraan, CRUD Layout Peta, Akses Log Aktifitas, Cetak struk parkir
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('area-parkir', \App\Http\Controllers\AreaParkirController::class);
        Route::resource('kendaraan', \App\Http\Controllers\KendaraanController::class);
        Route::resource('tarif', \App\Http\Controllers\TarifController::class);
        Route::resource('parking-maps', ParkingMapController::class);
        Route::resource('log-aktivitas', \App\Http\Controllers\LogAktifitasController::class);
        Route::resource('kamera', \App\Http\Controllers\CameraController::class);
        Route::get('/transaksi/{id}/print', [\App\Http\Controllers\TransaksiController::class, 'print'])->name('transaksi.print');
    });

    // Transaksi: index & show untuk Admin dan Petugas (lihat riwayat tanpa edit/hapus)
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::get('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/create-check-in', function () {
            $kendaraans = \App\Models\Kendaraan::orderBy('plat_nomor')->get();
            $tarifs = \App\Models\Tarif::orderBy('jenis_kendaraan')->get();
            $areas = \App\Models\AreaParkir::orderBy('nama_area')->get();
            $cameras = \App\Models\Camera::scanner()->orderBy('is_default', 'desc')->orderBy('id')->get();
            return view('parkir.create', compact('kendaraans', 'tarifs', 'areas', 'cameras'));
        })->name('transaksi.create-check-in');
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
        Route::get('/payment/{id_parkir}/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
        Route::get('/payment/{id_parkir}/midtrans', [\App\Http\Controllers\PaymentController::class, 'midtransPay'])->name('payment.midtrans');
        Route::post('/payment/{id_parkir}/midtrans/token', [\App\Http\Controllers\PaymentController::class, 'midtransSnapToken'])->name('payment.midtrans.token');
        Route::get('/payment-history', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
    });

    // ========== OWNER ONLY (sesuai SPK: Rekap transaksi sesuai waktu) ==========
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/report/pembayaran', [\App\Http\Controllers\ReportController::class, 'pembayaran'])->name('report.pembayaran');
        Route::get('/report/transaksi', [\App\Http\Controllers\ReportController::class, 'transaksi'])->name('report.transaksi');
        Route::get('/report/pembayaran/export/csv', [\App\Http\Controllers\ReportController::class, 'exportPembayaranCSV'])->name('report.pembayaran.export-csv');
        Route::get('/report/transaksi/export/csv', [\App\Http\Controllers\ReportController::class, 'exportTransaksiCSV'])->name('report.transaksi.export-csv');
    });
});
