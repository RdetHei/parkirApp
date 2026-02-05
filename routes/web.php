<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkirController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\PetugasDashboardController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Public signed QR confirmation (no auth required, but requires valid signature)
Route::get('/payment/{id_parkir}/confirm-qr/signed', [\App\Http\Controllers\PaymentController::class, 'confirm_qr_signed'])
    ->name('payment.confirm-qr.signed')
    ->middleware('signed');

// Auth Routes
Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::get('/login', [LoginController::class, 'create'])->name('login.create');
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('throttle:5,1');

// Protected Routes - Require Authentication
Route::middleware(['auth'])->group(function () {
    // API for Parking Map (SVG 2D)
    Route::get('/api/parking-slots', [\App\Http\Controllers\Api\ParkingMapController::class, 'index'])->name('api.parking-slots');
    Route::post('/api/parking-slots/{area_id}/bookmark', [\App\Http\Controllers\Api\ParkingMapController::class, 'bookmark'])->name('api.parking-slots.bookmark');
    Route::post('/api/parking-slots/{id_transaksi}/unbookmark', [\App\Http\Controllers\Api\ParkingMapController::class, 'unbookmark'])->name('api.parking-slots.unbookmark');

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

    // Peta Parkir (semua role yang login)
    Route::get('/parking-map', [\App\Http\Controllers\Api\ParkingMapController::class, 'showMap'])->name('parking.map.index');

    // ========== ADMIN ONLY (sesuai Tabel Fitur SPK) ==========
    // Admin: CRUD User, CRUD Tarif, CRUD Area Parkir, CRUD Kendaraan, Akses Log Aktifitas, Cetak struk parkir
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('area-parkir', \App\Http\Controllers\AreaParkirController::class);
        Route::resource('kendaraan', \App\Http\Controllers\KendaraanController::class);
        Route::resource('tarif', \App\Http\Controllers\TarifController::class);
        Route::resource('log-aktivitas', \App\Http\Controllers\LogAktifitasController::class);
        Route::get('/transaksi/{id}/print', [\App\Http\Controllers\TransaksiController::class, 'print'])->name('transaksi.print');
    });

    // Transaksi: index & show untuk Admin dan Petugas (lihat riwayat tanpa edit/hapus)
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::get('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/create-check-in', function () {
            $kendaraans = \App\Models\Kendaraan::orderBy('plat_nomor')->get();
            $tarifs = \App\Models\Tarif::orderBy('jenis_kendaraan')->get();
            $areas = \App\Models\AreaParkir::orderBy('nama_area')->get();
            return view('parkir.create', compact('kendaraans', 'tarifs', 'areas'));
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
        Route::get('/payment/{id_parkir}/manual', [\App\Http\Controllers\PaymentController::class, 'manual_confirm'])->name('payment.manual-confirm');
        Route::post('/payment/{id_parkir}/manual', [\App\Http\Controllers\PaymentController::class, 'manual_process'])->name('payment.manual-process');
        Route::get('/payment/{id_parkir}/qr', [\App\Http\Controllers\PaymentController::class, 'qr_scan'])->name('payment.qr-scan');
        Route::post('/payment/{id_parkir}/confirm-qr', [\App\Http\Controllers\PaymentController::class, 'confirm_qr'])->name('payment.confirm-qr');
        Route::get('/payment/{id_parkir}/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
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
