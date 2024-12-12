<?php

namespace App\Http\Requests\User\Profile;

use App\Http\Requests\FormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property-read ?string $name
 * @property-read ?string $email
 * @property-read ?string $password
 * @property-read ?string $password_confirmation
 * @property-read ?string $cpf
 * @property-read ?string $current_password
 */
final class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['string', 'between:4,50'],
            'email' => ['email', Rule::validEmail(), Rule::unique(User::class)->ignore($user)],
            'password' => [Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required_with:password', 'same:password'],

            'cpf' => ['nullable', 'string', Rule::cpf(), Rule::unique(User::class)->ignore($user)],

            'current_password' => ['required_with:email,password', 'current_password'],
        ];
    }
}
