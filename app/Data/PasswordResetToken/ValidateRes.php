<?php

namespace App\Data\PasswordResetToken;

use App\Enums\Auth\PasswordTokenStatus;
use Spatie\LaravelData\Data;

final class ValidateRes extends Data
{
    public function __construct(
        public PasswordTokenStatus $token_status,
    ) {
    }
}
