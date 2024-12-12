<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use App\Models\User;
use App\RateLimiters\RecoverPasswordRateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @property-read string $email
 */
final class RecoverPasswordRequest extends FormRequest
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
        app(RecoverPasswordRateLimiter::class)->assertAttempts($this);
        app(RecoverPasswordRateLimiter::class)->hit($this);
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
        ];
    }
}
