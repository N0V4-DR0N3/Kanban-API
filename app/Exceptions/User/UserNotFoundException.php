<?php

namespace App\Exceptions\User;

use App\Exceptions\ModelNotFoundException;
use App\Models\User;

/**
 * @extends ModelNotFoundException<User>
 * @phpstan-extends ModelNotFoundException<User>
 */
final class UserNotFoundException extends ModelNotFoundException
{
    public function __construct(?string $message = null, array|int|string $ids = [])
    {
        $message ??= __('exceptions.user.not_found');
        parent::__construct(model: User::class, message: $message, ids: $ids);
    }
}
