<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkirController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

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
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

// Protected Routes - Require Authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Parkir Routes
    Route::get('/parkir-aktif', [\App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.parkir.index')->defaults('status', 'masuk');
    Route::get('/transaksi/create-check-in', function() {
        $kendaraans = \App\Models\Kendaraan::orderBy('plat_nomor')->get();
        $tarifs = \App\Models\Tarif::orderBy('jenis_kendaraan')->get();
        $areas = \App\Models\AreaParkir::orderBy('nama_area')->get();
        return view('parkir.create', compact('kendaraans', 'tarifs', 'areas'));
    })->name('transaksi.create-check-in');
    Route::post('/transaksi/check-in', [\App\Http\Controllers\TransaksiController::class, 'checkIn'])->name('transaksi.checkIn');
    Route::put('/transaksi/{id}/create-check-out', [\App\Http\Controllers\TransaksiController::class, 'checkOut'])->name('transaksi.create-check-out');
    Route::resource('transaksi', \App\Http\Controllers\TransaksiController::class);
    Route::get('/transaksi/{id}/print', [\App\Http\Controllers\TransaksiController::class, 'print'])->name('transaksi.print');

    // Admin Routes - User Management
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
    });

    // Admin Routes - Sistem Management
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::resource('area-parkir', \App\Http\Controllers\AreaParkirController::class);
        Route::resource('kendaraan', \App\Http\Controllers\KendaraanController::class);
        Route::resource('tarif', \App\Http\Controllers\TarifController::class);
        Route::resource('log-aktivitas', \App\Http\Controllers\LogAktifitasController::class);
    });

    // Payment Routes
    Route::get('/payment/select-transaction', [\App\Http\Controllers\PaymentController::class, 'selectTransaction'])->name('payment.select-transaction');
    Route::get('/payment/{id_parkir}', [\App\Http\Controllers\PaymentController::class, 'create'])->name('payment.create');
    Route::get('/payment/{id_parkir}/manual', [\App\Http\Controllers\PaymentController::class, 'manual_confirm'])->name('payment.manual-confirm');
    Route::post('/payment/{id_parkir}/manual', [\App\Http\Controllers\PaymentController::class, 'manual_process'])->name('payment.manual-process');
    Route::get('/payment/{id_parkir}/qr', [\App\Http\Controllers\PaymentController::class, 'qr_scan'])->name('payment.qr-scan');
    Route::post('/payment/{id_parkir}/confirm-qr', [\App\Http\Controllers\PaymentController::class, 'confirm_qr'])->name('payment.confirm-qr');
    Route::get('/payment/{id_parkir}/success', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment-history', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');

    // Report Routes
    Route::middleware(['role:admin,petugas'])->group(function () {
        Route::get('/report/pembayaran', [\App\Http\Controllers\ReportController::class, 'pembayaran'])->name('report.pembayaran');
        Route::get('/report/transaksi', [\App\Http\Controllers\ReportController::class, 'transaksi'])->name('report.transaksi');
        Route::get('/report/pembayaran/export/csv', [\App\Http\Controllers\ReportController::class, 'exportPembayaranCSV'])->name('report.pembayaran.export-csv');
        Route::get('/report/transaksi/export/csv', [\App\Http\Controllers\ReportController::class, 'exportTransaksiCSV'])->name('report.transaksi.export-csv');
    });
});

