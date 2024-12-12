<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class SetAuthGuard
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @param string $guard
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $guard): mixed
    {
        Auth::shouldUse($guard);

        return $next($request);
    }
}
