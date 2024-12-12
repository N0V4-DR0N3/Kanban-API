<?php

namespace App\Http\Controllers;

use App\Data\Auth\AuthRes;
use App\Data\Auth\LoginRes;
use App\Data\PasswordResetToken\ValidateRes;
use App\Data\User\ResetPasswordData;
use App\Exceptions\Auth\UnauthorizedException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RecoverPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\PasswordResetToken\ValidateRequest;
use App\Http\Resources\DataResource;
use App\RateLimiters\LoginRateLimiter;
use App\Services\AuthService;
use App\Services\PasswordResetTokenService;
use Illuminate\Http\Response;
use Throwable;

final class AuthController extends Controller
{
    readonly AuthService $service;
    readonly PasswordResetTokenService $passResetService;

    /**
     * @route GET /auth
     *
     * @return DataResource<AuthRes>
     * @throws
     */
    public function index(): DataResource
    {
        $user = $this->resolveUser();

        throw_unless($user, UnauthorizedException::class);

        return new DataResource(new AuthRes(
            device_id: $user->token()->id,
        ));
    }

    /**
     * @route POST /auth/login
     *
     * @return DataResource<LoginRes>
     */
    public function login(LoginRequest $request): DataResource
    {
        $login = $this->service->login($request->email, $request->password);

        app(LoginRateLimiter::class)->clear($request);

        return new DataResource(new LoginRes(
            device_id: $login->token->id,
            token: $login->accessToken,
        ));
    }

    /**
     * @route POST /auth/logout
     *
     * @throws Throwable
     */
    public function logout(): Response
    {
        $this->service->logout();

        return response()->noContent();
    }

    /**
     * @route POST /auth/recover-password
     *
     * @throws Throwable
     */
    public function recoverPassword(RecoverPasswordRequest $request): Response
    {
        $this->service->recoverPassword($request->email);

        return response()->noContent();
    }

    /**
     * @route POST /auth/reset-password
     *
     * @throws Throwable
     */
    public function resetPassword(ResetPasswordRequest $request): Response
    {
        $this->service->resetPassword(
            data: ResetPasswordData::fromRequest($request),
        );

        return response()->noContent();
    }

    /**
     * @route POST /auth/password-reset-tokens/validate
     *
     * @return DataResource<ValidateRes>
     */
    public function validatePasswordResetToken(ValidateRequest $request): DataResource
    {
        $passToken = $this->passResetService->find($request->email, $request->token);
        $tokenStatus = $this->passResetService->validate($passToken);

        return new DataResource(new ValidateRes(
            token_status: $tokenStatus,
        ));
    }
}
