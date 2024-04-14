<?php

namespace App\Jobs;

use App\Concerns\Modifiers\InjectsReadonly;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * @mixin InjectsReadonly
 * @mixin Dispatchable
 * @mixin InteractsWithQueue
 * @mixin Queueable
 */
abstract class Job implements ShouldQueue
{
    use InjectsReadonly;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function handle(): void
    {
        $this->injectReadonly();
        if (method_exists($this, 'run')) {
            $this->run();
        }
    }
}
