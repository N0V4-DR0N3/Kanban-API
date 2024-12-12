<?php

namespace App\Exceptions\User;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class DuplicateEmailException extends BadRequestHttpException
{
    public function __construct(?string $message = null)
    {
        $message ??= __('exceptions.user.duplicate_email');
        parent::__construct($message);
    }
}
