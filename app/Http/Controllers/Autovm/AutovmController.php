<?php

namespace App\Http\Controllers\Autovm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutovmController extends Controller
{
    public function token()
    {
        return [
            'token' => config('autovm.token'),
        ];
    }
}
