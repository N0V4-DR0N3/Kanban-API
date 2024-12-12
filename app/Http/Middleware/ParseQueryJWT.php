<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class ParseQueryJWT
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($jwt = $request->jwt) {
            $request->headers->set('Authorization', "Bearer {$jwt}");
        }

        return $next($request);
    }
}
