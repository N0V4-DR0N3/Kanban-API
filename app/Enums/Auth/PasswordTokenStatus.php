<?php

namespace App\Enums\Auth;

enum PasswordTokenStatus: string
{
    case OK = 'ok';
    case EXPIRED = 'expired';
    case NONEXISTENT = 'nonexistent';
}
