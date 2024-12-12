<?php

use App\Http\Middleware\BlockInProduction;
use App\Http\Middleware\HasPermissions;
use App\Http\Middleware\ParseQueryJWT;
use App\Http\Middleware\ReturnJson;
use App\Http\Middleware\SetAuthGuard;
use App\Services\Facades\LogFacadeService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

require_once 'aliases.php';
require_once 'helpers.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web/index.php',
        api: __DIR__.'/../routes/api/index.php',
        commands: __DIR__.'/../routes/console.php',

        health: '/up',
    )
    ->withCommands(
        commands: [__DIR__.'/../app/Console/Commands'],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'guard' => SetAuthGuard::class,
            'local' => BlockInProduction::class,
            'permissions' => HasPermissions::class,
        ]);

        $middleware->prepend([
            ParseQueryJWT::class,
        ]);
        $middleware->prependToGroup(group: 'web', middleware: ['guard:web']);
        $middleware->prependToGroup(group: 'api', middleware: [
            'guard:api',
            ReturnJson::class,
        ]);

        $middleware->throttleApi('api')->throttleWithRedis();
    })
    ->booted(static function () {
        RateLimits:

        RateLimiter::for(name: 'api', callback: static function (Request $request) {
            $key = $request->user()?->id ?? $request->ip();

            return Limit::perMinute(maxAttempts: 100)->by($key);
        });
    })
    ->withEvents()
    ->withExceptions()
->withSingletons([
        'logFacadeService' => static fn () => app(LogFacadeService::class),
    ])
    ->create();
