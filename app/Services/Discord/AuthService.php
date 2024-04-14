<?php

namespace App\Services\Discord;

use App\Concerns\Modifiers\InjectsReadonly;
use App\Models\User\User;
use App\Repositories\User\UserOauthTokenRepository;
use App\Services\User\AuthTokenService;
use App\Services\User\UserOauthTokenService;
use App\Services\User\UserService;
use Illuminate\Support\Carbon;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class AuthService extends GenericProvider
{
    use InjectsReadonly;

    protected readonly AuthTokenService $authTokenService;
    protected readonly UserService $userService;

    public function __construct()
    {
        parent::__construct([
            'clientId' => config('discord.client_id'),
            'clientSecret' => config('discord.client_secret'),
            'redirectUri' => config('discord.redirect_uri'),
            'scopes' => config('discord.scopes'),

            'urlAuthorize' => config('discord.authorization_url'),
            'urlAccessToken' => config('discord.token_url'),
            'urlResourceOwnerDetails' => config('discord.resource_owner_url'),
        ]);

        $this->injectReadonly();
    }

    /**
     * @throws Throwable
     */
    public function authorizedUser(AccessToken $token, GenericResourceOwner $genericResourceOwner): User
    {
        $owner = $genericResourceOwner->toArray();
        $provider_id = $owner['id'];

        if (!in_array($provider_id, config('discord.allowed_ids'))) {
            throw new UnauthorizedHttpException('Your user cannot login here');
        }

        if ($authTokenProvider = $this->authTokenService->findByProviderId($owner['id'])) {

            $avatar = $owner['avatar'];
            $color = $owner['banner_color'];
            $discriminator = $owner['discriminator'];
            $username = $owner['username'];

            $this->authTokenService->update($authTokenProvider, [
                'provider' => $authTokenProvider->provider,
                'access_token' => $token->getToken(),
                'refresh_token' => $token->getRefreshToken(),
                'expires_at' => Carbon::parse($token->getExpires()),

                'provider_id' => $provider_id,
                'provider_avatar' => $avatar,
                'provider_discriminator' => $discriminator,
                'provider_username' => $username,
                'color' => $color,
            ]);
            return $authTokenProvider->user;
        }

        return $this->userService->createDiscordUser($token, $genericResourceOwner);
    }
}
