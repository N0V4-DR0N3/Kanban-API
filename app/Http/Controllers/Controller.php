<?php

namespace App\Http\Controllers;

use App\Concerns\Modifiers\InjectsReadonly;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests;
    use InjectsReadonly;
    use ValidatesRequests;

    public function __construct()
    {
        $this->injectReadonly();
    }
}
