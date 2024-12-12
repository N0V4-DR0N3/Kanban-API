<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property-read string $role_id
 * @property-read string $name
 * @property-read string $email
 * @property-read string $password
 * @property-read string $password_confirmation
 * @property-read ?string $cpf
 * @property-read ?bool $active
 */
final class StoreRequest extends FormRequest
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
        return [
            'role_id' => ['required', 'string', Rule::exists(Role::class, 'id')],

            'name' => ['required', 'string', 'between:4,50'],
            'email' => [
                'required', 'email',
                Rule::validEmail(),
                Rule::unique(User::class, 'email')->whereNull('deleted_at'),
            ],
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'password_confirmation' => ['required', 'same:password'],

            'cpf' => [
                'nullable', 'string',
                Rule::cpf(),
                Rule::unique(User::class, 'cpf')->whereNull('deleted_at'),
            ],

            'active' => ['boolean'],
        ];
    }
}
