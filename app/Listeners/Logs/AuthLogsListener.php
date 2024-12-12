<?php

namespace App\Listeners\Logs;

use App\Events\Auth\AuthLogin;
use App\Events\Auth\AuthLoginFailed;
use App\Events\Auth\AuthLogout;
use App\Facades\Log;
use Illuminate\Events\Dispatcher;

final class AuthLogsListener extends LogsListener
{
    public function subscribe(Dispatcher $events): array
    {
        return [
            AuthLogin::class => 'handleLogin',
            AuthLoginFailed::class => 'handleLoginFailed',
            AuthLogout::class => 'handleLogout',
        ];
    }

    public function handleLogin(AuthLogin $e): void
    {
        Log::auth_login($e->user);
    }

    public function handleLoginFailed(AuthLoginFailed $e): void
    {
        Log::auth_loginFailed($e->user, $e->reason);
    }

    public function handleLogout(AuthLogout $e): void
    {
        Log::auth_logout($e->user);
    }
}
