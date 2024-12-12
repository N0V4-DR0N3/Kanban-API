<?php

namespace App\Exceptions\User;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CannotDeleteException extends BadRequestHttpException
{
    public function __construct(?string $message = null)
    {
        $message ??= __('exceptions.user.cannot_delete');
        parent::__construct($message);
    }
}
