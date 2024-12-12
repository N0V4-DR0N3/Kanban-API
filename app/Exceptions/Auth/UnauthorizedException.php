<?php

namespace App\Exceptions\Auth;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class UnauthorizedException extends UnauthorizedHttpException
{
    public function __construct(string $message = 'Você não está autenticado na aplicação.')
    {
        parent::__construct('Bearer', $message);
    }
}
