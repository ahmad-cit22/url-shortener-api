<?php

use App\Http\Controllers\Api\V2\UrlController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/shorten', [UrlController::class, 'shortenUrl']);
    Route::get('/urls', [UrlController::class, 'listUrls']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
