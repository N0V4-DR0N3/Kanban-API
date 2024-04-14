<?php

namespace App\Services;

use App\Concerns\Modifiers\InjectsReadonly;

/**
 * @mixin InjectsReadonly
 */
abstract class Service
{
    use InjectsReadonly;

    public function __construct()
    {
        $this->injectReadonly();
    }
}
