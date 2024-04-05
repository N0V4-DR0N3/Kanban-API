<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OauthAuthorizeRequest;
use App\Services\Auth\AuthService;
use App\Services\Discord\AuthService as DiscordAuthService;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Throwable;

class DiscordAuthController extends Controller
{
    protected readonly AuthService $service;
    protected readonly DiscordAuthService $discordAuthService;

    public function authUrl()
    {
        return [
            'url' => $this->discordAuthService->getAuthorizationUrl(),
        ];
    }

    /**
     * @throws Throwable
     * @throws IdentityProviderException
     */
    public function authorizeLogin(OauthAuthorizeRequest $request)
    {
        $discordToken = $this->discordAuthService->getAccessToken('authorization_code', [
            'code' => $request->code,
        ]);
        $owner = $this->discordAuthService->getResourceOwner($discordToken);

        $user = $this->discordAuthService->authorizedUser($discordToken, $owner);
        $token = $this->service->createToken($user);

        return compact('token', 'user');
    }
}
