<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final class ValidEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $assert = preg_match('/^\w+(\.\w+)*@\w+(\.\w+)+$/i', $value) === 1;

        if (!$assert) {
            $fail(__('rules.email', compact('attribute')));
        }
    }
}
