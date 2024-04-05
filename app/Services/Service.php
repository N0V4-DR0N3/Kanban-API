<?php

namespace App\Services;

use App\Concerns\Modifiers\InjectsReadonly;

class Service
{
    use InjectsReadonly;

    public function __construct()
    {
        $this->injectReadonly();
    }
}
