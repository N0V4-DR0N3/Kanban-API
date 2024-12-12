<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use App\Models\User;
use App\RateLimiters\LoginRateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @property-read string $email
 * @property-read string $password
 */
final class LoginRequest extends FormRequest
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
        app(LoginRateLimiter::class)->assertAttempts($this);
        app(LoginRateLimiter::class)->hit($this);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required', 'email',
                Rule::validEmail(),
                Rule::exists(User::class, 'email'),
            ],
            'password' => [
                'required', 'string', 'min:6',
            ],
        ];
    }
}
