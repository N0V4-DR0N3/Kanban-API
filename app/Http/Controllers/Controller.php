<?php

namespace App\Http\Controllers;

use App\Concerns\Modifiers\InjectsReadonly;

abstract class Controller
{
    use InjectsReadonly;

    public function __construct()
    {
        $this->injectReadonly();
    }
}