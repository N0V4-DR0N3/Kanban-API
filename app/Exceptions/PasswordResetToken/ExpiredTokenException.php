<?php

namespace App\Exceptions\PasswordResetToken;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ExpiredTokenException extends BadRequestHttpException
{
    public function __construct(string $message = 'Este token de recuperação já expirou.')
    {
        parent::__construct($message);
    }
}
