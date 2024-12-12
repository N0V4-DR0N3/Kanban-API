<?php

namespace App\Data\PasswordResetToken;

use Spatie\LaravelData\Data;

final class InsertData extends Data
{
    public function __construct(
        public string $email,
        public string $token,
    ) {
    }
}
