<?php

namespace App\RateLimiters;

use Illuminate\Http\Request;

final class LoginRateLimiter extends RateLimiter
{
    protected int $maxAttempts = 3;
    protected int $decaySeconds = 60 * 5;

    protected function key(Request $request): string
    {
        return "login|{$request->ip()}";
    }
}
