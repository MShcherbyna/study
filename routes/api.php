<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Booking;

Route::middleware('auth.apikey')->prefix('v1')->group(function () {
    Route::controller(Booking::class)->prefix('bookings')->group(function () {
        Route::get('/{uuid}', 'show');
        Route::get('/', 'index');
        Route::post('/', 'store');
    });
});
