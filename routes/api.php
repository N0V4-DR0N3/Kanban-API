<?php

use App\Http\Controllers\Auth\DiscordAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::prefix('discord')->controller(DiscordAuthController::class)->group(function () {
        Route::get('url', 'authUrl');

        Route::prefix('authorize')->group(function () {
            Route::get('', 'authorizeLogin');
            Route::post('', 'authorizeLogin');
        });
    });
});
