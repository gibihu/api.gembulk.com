<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth.v1')->group(function () {
    require __DIR__.'/apis/v1.php';
});

