<?php

use App\Http\Controllers\Apis\V1\Sendings\OTPSendingV1ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::prefix('s')->name('sendings.')->group(function () {
        Route::controller(OTPSendingV1ApiController::class)->prefix('otp')->name('otp.')->group(function () {
            Route::post('/send', 'send')->name('send');
            Route::get('/{campaign_id}', 'report')->name('report');
        });
    });
});
