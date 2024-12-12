<?php

namespace App\Listeners;

use App\Traits\InjectsReadonly;

abstract class Listener
{
    use InjectsReadonly;

    /**
     * Create the event listener.
     */
    public final function __construct()
    {
        $this->injectReadonly();
        $this->__setup();
    }

    public function __setup(): void
    {
    }
}
