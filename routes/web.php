<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkirController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;



Route::get('/', function () {
    return view('welcome');
});

// Registration Routes
Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Login & Logout Routes
Route::get('/login', [LoginController::class, 'create'])->name('login.create');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::get('/parkir', [ParkirController::class, 'index'])->name('parkir.index');

    Route::get('/parkir/daftar', [ParkirController::class, 'create'])->name('parkir.create');

    Route::post('/parkir', [ParkirController::class, 'store'])->name('parkir.store');

    Route::put('/parkir/{id}', [ParkirController::class, 'update'])->name('parkir.update');

     Route::get('/parkir/struk/{id}', [ParkirController::class, 'print'])->name('parkir.print');
    // User management (CRUD)
    Route::resource('users', \App\Http\Controllers\UserController::class);

    Route::resource('area-parkir', \App\Http\Controllers\AreaParkirController::class);

    Route::resource('kendaraan', \App\Http\Controllers\KendaraanController::class);

    Route::resource('tarif', \App\Http\Controllers\TarifController::class);

    Route::resource('transaksi', \App\Http\Controllers\TransaksiController::class);
    
    Route::resource('log-aktivitas', \App\Http\Controllers\LogAktifitasController::class);
});
