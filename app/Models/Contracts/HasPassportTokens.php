<?php

namespace App\Models\Contracts;

use App\Models\Passport\Token;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Passport\Client;
use Laravel\Passport\PersonalAccessTokenResult;

/**
 * @property-read Collection<int, Client> $clients
 * @property-read Collection<int, Token> $tokens
 */
interface HasPassportTokens
{
    /**
     * @return Collection<int, Client>
     */
    public function clients();

    /**
     * @return Collection<int, Token>
     */
    public function tokens();

    /**
     * @return ?Token
     */
    public function token();

    /**
     * @param string $scope
     *
     * @return bool
     */
    public function tokenCan($scope);

    /**
     * @param string $name
     * @param string[] $scopes
     *
     * @return PersonalAccessTokenResult
     */
    public function createToken($name, array $scopes = []);

    /**
     * @param Token $token
     *
     * @return static
     */
    public function withAccessToken($token);
}
