<?php

namespace App\Http\Requests\Task;

use App\Enums\Task\TaskStatus;
use App\Http\Requests\FormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @property-read ?string $title
 * @property-read ?string $description
 * @property-read ?string $status
 * @property-read ?string $limit_date
 *
 * @property-read ?string[] $responsibles
 */
class UpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title' => ['string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'limit_date' => ['date_format:Y-m-d'],
            'status' => ['string', Rule::enum(TaskStatus::class)],

            'responsibles' => ['array', 'min:1'],
            'responsibles.*' => ['string', Rule::exists(User::class, 'id')],
        ];
    }
}
