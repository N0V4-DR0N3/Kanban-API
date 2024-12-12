<?php

namespace App\Exceptions\Auth;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InvalidPasswordTokenException extends BadRequestHttpException
{
    public function __construct(string $message = 'Este token de recuperação de senha não é válido.')
    {
        parent::__construct($message);
    }
}
