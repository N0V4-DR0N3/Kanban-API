<?php

namespace App\Services\User;

use App\Models\User\UserOauthToken;
use App\Services\Service;
use Throwable;

class AuthTokenService extends Service
{
    protected readonly UserOauthToken $model;

    public function findByProviderId(string $id): ?UserOauthToken
    {
        /** @var  UserOauthToken $oauthToken */
        $oauthToken = $this->model->newQuery()->firstWhere('provider_id', $id);
        return $oauthToken;
    }

    public function update(UserOauthToKen $oauthToken, array $data): void
    {
        $oauthToken->fill($data);
        $oauthToken->saveOrFail();
    }

    /**
     * @throws Throwable
     */
    public function create(array $data): UserOauthToken
    {
        $oauthToken = $this->model->newInstance();
        $oauthToken->fill($data);
        $oauthToken->saveOrFail();
        return $oauthToken;
    }
}
