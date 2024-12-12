<?php

namespace App\Repositories;

use App\Data\PasswordResetToken\InsertData;
use App\Models\PasswordResetToken;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * @extends Repository<PasswordResetToken>
 * @phpstan-extends Repository<PasswordResetToken>
 */
final class PasswordResetTokenRepository extends Repository
{
    /** @var PasswordResetToken */
    protected Model $model;

    public function __construct()
    {
        parent::__construct(new PasswordResetToken);
    }

    /**
     * @throws Throwable
     */
    public function create(InsertData $data): PasswordResetToken
    {
        return $this->_create([
            'email' => $data->email,
            'token' => $data->token,
        ]);
    }

    public function delete(PasswordResetToken $token): bool
    {
        return $this->query()->where([
            'email' => $token->email,
            'token' => $token->token,
        ])->delete() > 0;
    }
}
