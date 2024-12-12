<?php

namespace App\Http\Middleware;

use App\Exceptions\Auth\MissingPermissionsException;
use App\Exceptions\Auth\UnauthorizedException;
use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;

final class HasPermissions extends PermissionMiddleware
{
    public function handle(mixed $request, Closure $next, mixed $permission, mixed $guard = null): mixed
    {
        try {
            return parent::handle($request, $next, $permission, $guard);
        } catch (SpatieUnauthorizedException $e) {
            $msg = str($e->getMessage());

            if ($msg->startsWith('User is not logged in.')) {
                throw new UnauthorizedException;
            }
            if ($msg->startsWith('User does not have the right permissions.')) {
                throw new MissingPermissionsException;
            }

            throw $e;
        }
    }
}
