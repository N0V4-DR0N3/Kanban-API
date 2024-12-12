<?php

namespace App\Http\Requests\PasswordResetToken;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @property-read string $email
 * @property-read string $token
 */
final class ValidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !Auth::check();
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
            ],
            'token' => ['required', 'string'],
        ];
    }
}
