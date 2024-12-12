<?php

namespace App\Services;

use App\Traits\InjectsReadonly;

abstract class Service
{
    use InjectsReadonly;

    final public function __construct()
    {
        $this->injectReadonly();
        $this->__setup();
    }

    protected function __setup(): void
    {
        //
    }
}
