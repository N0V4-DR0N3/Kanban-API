<?php

namespace App\Exceptions\User;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CannotSelfUpdateException extends BadRequestHttpException
{
    public function __construct(?string $message = null)
    {
        $message ??= __('exceptions.user.cannot_self_update');
        parent::__construct($message);
    }
}
