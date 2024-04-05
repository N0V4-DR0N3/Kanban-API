<?php

namespace App\Services\Auth;

use App\Enums\Auth\AuthProviders;
use App\Models\User\User;
use App\Services\Service;

class AuthService extends Service
{
    public function createToken(User $user)
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function getAvatar(User $user)
    {
        $provider = $user->oauthToken;
        if (!$provider) {
            return null;
        }

        return match ($provider->provider) {
            AuthProviders::DISCORD => "https://cdn.discordapp.com/avatars/{$provider->provider_id}/{$provider->provider_avatar}.webp",
        };
    }
}
