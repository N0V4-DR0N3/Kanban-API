<?php

namespace App\Listeners\Logs;

use App\Listeners\Listener;
use App\Models\User;

abstract class LogsListener extends Listener
{
    protected function isSelf(User $user): bool
    {
        return $user->id === request()->user()?->id;
    }
}
