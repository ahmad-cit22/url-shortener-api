<?php

use App\Http\Controllers\Api\V1\UrlController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'test';
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/shorten', [UrlController::class, 'shortenUrl']);
    Route::get('/urls', [UrlController::class, 'listUrls']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
