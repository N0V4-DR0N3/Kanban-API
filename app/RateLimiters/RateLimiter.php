<?php

namespace App\RateLimiters;

use Illuminate\Cache\RateLimiter as BaseRateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

abstract class RateLimiter
{
    protected int $maxAttempts = 3;
    protected int $decaySeconds = 60 * 5;

    public function __construct(
        protected BaseRateLimiter $limiter
    ) {
    }

    abstract protected function key(Request $request): string;

    public function attempts(Request $request): int
    {
        return $this->limiter->attempts($this->key($request));
    }

    public function remaining(Request $request): int
    {
        return $this->limiter->remaining($this->key($request), $this->maxAttempts);
    }

    public function tooManyAttempts(Request $request): bool
    {
        return $this->limiter->tooManyAttempts($this->key($request), $this->maxAttempts);
    }

    public function hit(Request $request): int
    {
        return $this->limiter->hit($this->key($request), $this->decaySeconds);
    }

    public function availableIn(Request $request): int
    {
        return $this->limiter->availableIn($this->key($request));
    }

    public function availableAt(Request $request): Carbon
    {
        return now()->addSeconds($this->availableIn($request));
    }

    public function clear(Request $request): void
    {
        $this->limiter->clear($this->key($request));
    }

    public function assertAttempts(Request $request, ?Response $response = null): false
    {
        if (!$this->tooManyAttempts($request)) {
            return false;
        }

        throw $this->buildException($request, $response);
    }

    /**
     * @param Request $request
     * @param ?Response $response
     *
     * @return array<string, string|int>
     */
    public function buildExceptionHeaders(Request $request, ?Response $response = null): array
    {
        $max = $this->maxAttempts;
        $remaining = $this->remaining($request);
        $responseRemaining = $response?->headers->get('X-RateLimit-Remaining');

        if (
            $response &&
            $responseRemaining !== null &&
            (int) $responseRemaining <= $remaining
        ) {
            return [];
        }

        return [
            'X-RateLimit-Limit' => $max,
            'X-RateLimit-Remaining' => $remaining,

            ...!$remaining ? [
                'Retry-After' => $this->availableIn($request),
                'X-RateLimit-Reset' => $this->availableAt($request)->unix(),
            ] : [],
        ];
    }

    public function buildException(Request $request, ?Response $response = null): ThrottleRequestsException
    {
        $headers = $this->buildExceptionHeaders($request, $response);

        return new ThrottleRequestsException('Too Many Attempts.', null, $headers);
    }
}
