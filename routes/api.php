<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ANPRController;
use App\Http\Controllers\Api\RfidParkingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/anpr-detection', [ANPRController::class, 'handleDetection'])->name('api.anpr.detection');

Route::prefix('rfid')->group(function () {
    Route::post('/checkin', [RfidParkingController::class, 'checkin']);
});

