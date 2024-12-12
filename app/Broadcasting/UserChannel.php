<?php

namespace App\Broadcasting;

use App\Models\User;

final class UserChannel extends PrivateChannel
{
    public function __construct(
        public string $user_id,
    ) {
        parent::__construct("user.{$user_id}");
    }

    public static function route(): string
    {
        return 'user.{user}';
    }

    public static function authorize(User $self, User $user): bool
    {
        foreach ([
            static fn () => $self->id === $user->id,
        ] as $fn) {
            if ($fn()) {
                return true;
            }
        }

        return false;
    }
}
