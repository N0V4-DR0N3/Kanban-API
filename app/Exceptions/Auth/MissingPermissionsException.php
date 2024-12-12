<?php

namespace App\Exceptions\Auth;

use Symfony\Component\Finder\Exception\AccessDeniedException;

final class MissingPermissionsException extends AccessDeniedException
{
    public function __construct(string $message = 'Permissões insuficientes.')
    {
        parent::__construct($message);
    }
}
