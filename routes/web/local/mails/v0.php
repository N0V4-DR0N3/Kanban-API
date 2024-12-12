<?php

use App\Mail\Auth\PasswordRecoveryMail;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mail Routes
|--------------------------------------------------------------------------
|
| The routes registered here are only available when the app is not in
| production. They will be available through the web guard using the
| "/mails" prefix.
|
*/

//Route::prefix('auth')->group(static function () {
//
//    Route::get('password-recovery', static function () {
//        return new PasswordRecoveryMail(User::first(), 'TOKEN');
//    });
//
//});
