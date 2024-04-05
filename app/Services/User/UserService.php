<?php

namespace App\Services\User;

use App\Enums\Auth\AuthProviders;
use App\Models\User\User;
use App\Services\Service;
use Illuminate\Support\Carbon;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Throwable;

class UserService extends Service
{
    protected readonly User $model;
    protected readonly AuthTokenService $authTokenService;

    /**
     * @throws Throwable
     */
    public function createDiscordUser(AccessToken $token, GenericResourceOwner $genericResourceOwner)
    {
        $provider = AuthProviders::DISCORD;
        $owner = $genericResourceOwner->toArray();

        $name = $owner['username'];
        $email = $owner['email'];

        $provider_id = $owner['id'];
        $avatar = $owner['avatar'];
        $color = $owner['banner_color'];
        $discriminator = $owner['discriminator'];
        $username = $owner['username'];

        $user = $this->model->newInstance();
        $user->name = $name;
        $user->email = $email;
        $user->saveOrFail();

        $this->authTokenService->create([
            'user_id' => $user->id,
            'provider' => $provider,
            'access_token' => $token->getToken(),
            'refresh_token' => $token->getRefreshToken(),
            'expires_at' => Carbon::parse($token->getExpires()),

            'provider_id' => $provider_id,
            'provider_avatar' => $avatar,
            'provider_discriminator' => $discriminator,
            'provider_username' => $username,
            'color' => $color,
        ]);
        return $user;
    }
}
