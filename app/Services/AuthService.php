<?php

namespace App\Services;

use App\Data\User\ResetPasswordData;
use App\Enums\Passport\TokenName;
use App\Events\Auth\AuthLogin;
use App\Events\Auth\AuthLoginFailed;
use App\Events\Auth\AuthLogout;
use App\Exceptions\User\UserInactiveException;
use App\Exceptions\User\UserNotFoundException;
use App\Models\Contracts\HasPassportTokens;
use App\Models\Passport\Token;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\PersonalAccessTokenResult;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

final class AuthService extends Service
{
    readonly protected UserService $userService;
    readonly protected UserRepository $userRepository;

    /**
     * @throws Throwable
     */
    public function login(string $email, string $password): PersonalAccessTokenResult
    {
        $user = $this->userRepository->findByEmail($email);

        throw_unless($user, UserNotFoundException::class);
        if (!$user->active) {
            event(new AuthLoginFailed($user, 'Usuário inativo.'));

            throw new UserInactiveException;
        }
        else if (!Hash::check($password, $user->password)) {
            event(new AuthLoginFailed($user, 'Senha incorreta.'));

            throw new AccessDeniedHttpException(__('exceptions.auth.invalid_password'));
        }

        Auth::setUser($user);
        event(new AuthLogin($user));

        return $this->issueToken($user, TokenName::APP_UI);
    }

    /**
     * @throws AccessDeniedHttpException|Throwable
     */
    public function logout(): bool
    {
        /** @var ?\App\Models\User $user */
        $user = Auth::user();

        throw_unless($user, new AccessDeniedHttpException);

        Auth::forgetUser();
        event(new AuthLogout($user));

        return $this->revokeToken($user->token());
    }

    public function issueToken(HasPassportTokens $tokenable, TokenName $tokenName): PersonalAccessTokenResult
    {
        return $tokenable->createToken($tokenName->value);
    }

    public function revokeToken(Token $token): bool
    {
        return $token->revoke();
    }

    /**
     * @param string $email
     *
     * @return User
     *
     * @throws Throwable
     * @throws UserNotFoundException
     * @throws UserInactiveException
     */
    public function recoverPassword(string $email): User
    {
        $user = $this->userRepository->findByEmail($email);

        throw_unless($user, UserNotFoundException::class);
        throw_unless($user->active, UserInactiveException::class);

        return $this->userService->recoverPassword($user);
    }

    /**
     * @param ResetPasswordData $data
     *
     * @return \App\Models\User
     *
     * @throws Throwable
     * @throws UserNotFoundException
     * @throws UserInactiveException
     */
    public function resetPassword(ResetPasswordData $data): User
    {
        $user = $this->userRepository->findByEmail($data->email);

        throw_unless($user, UserNotFoundException::class);
        throw_unless($user->active, UserInactiveException::class);

        return $this->userService->resetPassword($user, $data->token, $data->password);
    }
}
