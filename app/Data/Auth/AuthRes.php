<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;

final class AuthRes extends Data
{
    public function __construct(
        public string $device_id,
    ) {
    }
}
