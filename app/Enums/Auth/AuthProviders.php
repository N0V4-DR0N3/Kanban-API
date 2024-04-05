<?php

namespace App\Enums\Auth;

use App\Enums\Concerns\Collectable;

enum AuthProviders: string
{
    use Collectable;

    case DISCORD = 'discord';
    case PASSWORD = 'password';
}
