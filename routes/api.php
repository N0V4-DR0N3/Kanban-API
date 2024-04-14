<?php

use App\Http\Controllers\Auth\DiscordAuthController;
use App\Http\Controllers\Autovm\AutovmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('autovm')->controller(AutovmController::class)->group(function () {
        Route::get('token', 'token');
    });



    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::prefix('auth')->group(function () {
    Route::prefix('discord')->controller(DiscordAuthController::class)->group(function () {
        Route::get('url', 'authUrl');

        Route::prefix('authorize')->group(function () {
            Route::get('', 'authorizeLogin');
            Route::post('', 'authorizeLogin');
        });
    });
});
