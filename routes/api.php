<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkingSlotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint publik/stated-less untuk data slot parkir yang akan digunakan
| oleh peta Leaflet (indoor parking map).
|
*/

Route::get('/parking-slots', [ParkingSlotController::class, 'index'])->name('api.parking-slots.leaflet');

