<?php

namespace App\Notifications;

use App\Models\Model;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as BaseNotification;

abstract class Notification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    /**
     * @param Model $model
     *
     * @return array<string|class-string>
     */
    abstract public function via(Model $model): array;
}
