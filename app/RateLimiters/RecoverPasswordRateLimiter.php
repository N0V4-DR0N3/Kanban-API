<?php

namespace App\RateLimiters;

use Illuminate\Http\Request;

final class RecoverPasswordRateLimiter extends RateLimiter
{
    protected int $maxAttempts = 5;
    protected int $decaySeconds = 60 * 5;

    protected function key(Request $request): string
    {
        return "recover-password|{$request->ip()}";
    }
}
