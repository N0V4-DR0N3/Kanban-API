<?php

namespace App\Enums\Concerns;

trait Coerceable
{
    public static function coerce($value): static
    {
        if ($value instanceof static) {
            return $value;
        }

        return static::from($value);
    }

    public static function tryCoerce($value): ?static
    {
        if ($value instanceof static) {
            return $value;
        }

        return static::tryFrom($value);
    }
}
