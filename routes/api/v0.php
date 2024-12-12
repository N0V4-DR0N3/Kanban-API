<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

(static function () {
    // 🔴 Auth //

    Route::prefix('auth')->controller(AuthController::class)->group(static function () {
        Route::get('', 'index');
        Route::post('login', 'login');

        Route::post('recover-password', 'recoverPassword');
        Route::post('reset-password', 'resetPassword');

        Route::prefix('password-reset-tokens')->group(static function () {
            Route::post('validate', 'validatePasswordResetToken');
        });
    });

    Route::prefix('users')->controller(UserController::class)->group(static function () {
        Route::post('', 'store');
    });

})();

Route::middleware('auth:api')->group(static function () {
    // 🟢 Auth //

    Route::prefix('auth')->controller(AuthController::class)->group(static function () {
        Route::post('logout', 'logout');
    });

    Route::prefix('tasks')->controller(TaskController::class)->group(static function () {
        Route::get('', 'search');
        Route::post('', 'store');

        Route::prefix('{task}')->group(static function () {
            Route::get('', 'show');
            Route::patch('', 'update');
            Route::delete('', 'destroy');
        });
    });
});
