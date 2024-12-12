<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ServerErrorHttpException extends HttpException
{
    /**
     * @param string $message
     * @param ?Throwable $previous
     * @param int $code
     * @param array<string, string> $headers
     */
    public function __construct(string $message = '', ?Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(500, $message, $previous, $headers, $code);
    }
}
