<?php

use Illuminate\Support\Facades\Route;

Route::prefix('local')->group(__DIR__.'/local/index.php');
Route::prefix('')->group(__DIR__.'/v0.php');
