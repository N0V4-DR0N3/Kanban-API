<?php

namespace App\Exceptions\User;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UserInactiveException extends BadRequestHttpException
{
    public function __construct(?string $message = null)
    {
        $message ??= __('exceptions.user.inactive');
        parent::__construct($message);
    }
}
