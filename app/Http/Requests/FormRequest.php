<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\FiltersUnvalidated;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

/**
 * @method User user()
 */
abstract class FormRequest extends BaseFormRequest
{
    use FiltersUnvalidated;
}
