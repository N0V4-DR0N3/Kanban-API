<?php

namespace App\Jobs;

use App\Traits\InjectsReadonly;
use Illuminate\Foundation\Bus\Dispatchable;

abstract class Job
{
    use Dispatchable;
    use InjectsReadonly;

    protected function setup(): void
    {
        //
    }

    final public function handle(): void
    {
        $this->injectReadonly();
        $this->setup();

        if (method_exists($this, 'run')) {
            $this->run();
        }
    }
}
