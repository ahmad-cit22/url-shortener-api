<?php

use App\Http\Controllers\Api\V1\UrlController;
use App\Http\Controllers\Api\V2\UrlController as V2UrlController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/{url:short_url}', [UrlController::class, 'redirect'])->name('v1.redirect');
});

Route::prefix('v2')->group(function () {
    Route::get('/{url:short_url}', [V2UrlController::class, 'redirect'])->name('v2.redirect');
});
