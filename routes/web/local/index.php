<?php

use Illuminate\Support\Facades\Route;

Route::middleware('local')->group(function () {

    Route::prefix('mails')->group(__DIR__.'/mails/index.php');
    Route::prefix('reports')->group(__DIR__.'/reports/index.php');

});
