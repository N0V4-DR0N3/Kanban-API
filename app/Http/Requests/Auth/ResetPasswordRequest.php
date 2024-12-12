<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\RateLimiters\ResetPasswordRateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property-read string $token
 * @property-read string $email
 * @property-read string $password
 * @property-read string $password_confirmation
 */
final class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !Auth::check();
    }

    public function prepareForValidation(): void
    {
        app(ResetPasswordRateLimiter::class)->assertAttempts($this);
        app(ResetPasswordRateLimiter::class)->hit($this);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'token' => [
                'required', 'string',
                Rule::exists(PasswordResetToken::class, 'token'),
            ],
            'email' => [
                'required', 'email',
                Rule::validEmail(),
                Rule::exists(User::class, 'email'),
            ],
            'password' => [
                'required', 'string',
                Password::min(6)->letters()->mixedCase()->numbers()->symbols(),
            ],
            'password_confirmation' => [
                'required', 'same:password',
            ],
        ];
    }
}
