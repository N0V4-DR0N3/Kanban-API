<?php

namespace App\Services;

use App\Data\PasswordResetToken\InsertData;
use App\Enums\Auth\PasswordTokenStatus;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\PasswordResetToken\ExpiredTokenException;

//use App\Mail\Auth\PasswordRecoveryMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Repositories\PasswordResetTokenRepository;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Throwable;

/**
 * @final
 */
class PasswordResetTokenService extends Service
{
    readonly protected PasswordResetToken $model;
    readonly protected PasswordResetTokenRepository $repository;

    /**
     * @codeCoverageIgnore
     */
    public function find(string $email, string $token): ?PasswordResetToken
    {
        return $this->model->where(compact('email', 'token'))->first();
    }

    /**
     * @codeCoverageIgnore
     */
    public function findByUser(User $user): ?PasswordResetToken
    {
        return $this->model->where('email', $user->email)->first();
    }

    /**
     * @throws Throwable
     */
    public function create(User $user): PasswordResetToken
    {
        return DB::transaction(function () use ($user) {
            $this->deleteByUser($user);

            return $this->repository->create(new InsertData(
                email: $user->email,
                token: $this->getBrokerRepository()->createNewToken(),
            ));
        });
    }

    public function validate(?PasswordResetToken $token): PasswordTokenStatus
    {
        return match (true) {
            !$token => PasswordTokenStatus::NONEXISTENT,
            $token->expired => PasswordTokenStatus::EXPIRED,

            default => PasswordTokenStatus::OK,
        };
    }

    /**
     * @param PasswordTokenStatus $status
     *
     * @return void
     *
     * @throws ModelNotFoundException
     * @throws ExpiredTokenException
     */
    public function assertOk(PasswordTokenStatus $status): void
    {
        match ($status) {
            PasswordTokenStatus::NONEXISTENT => throw new ModelNotFoundException(PasswordResetToken::class),
            PasswordTokenStatus::EXPIRED => throw new ExpiredTokenException,

            default => null,
        };
    }

    /**
     * @codeCoverageIgnore
     */
    public function delete(PasswordResetToken $token): bool
    {
        return $this->repository->delete($token);
    }

    /**
     * @codeCoverageIgnore
     */
    public function deleteByUser(User $user): bool
    {
        if (!$token = $this->findByUser($user)) {
            return false;
        }

        return $this->delete($token);
    }

    public function sendRecoveryMail(User $user, string $token): void
    {
//        Mail::to($user)->queue(new PasswordRecoveryMail($user, $token));
    }

    protected function getBroker(): PasswordBroker
    {
        $guard = config('auth.defaults.passwords');

        return Password::broker($guard);
    }

    protected function getBrokerRepository(): DatabaseTokenRepository
    {
        /** @var DatabaseTokenRepository */
        return $this->getBroker()->getRepository();
    }

    public static function getExpirationMinutes(): int
    {
        $guard = config('auth.defaults.passwords');

        return config("auth.passwords.{$guard}.expire");
    }
}
