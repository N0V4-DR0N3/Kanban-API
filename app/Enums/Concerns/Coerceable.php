<?php

namespace App\Enums\Concerns;

trait Coerceable
{
    public static function coerce(mixed $value): static
    {
        if ($value instanceof static) {
            return $value;
        }

        return static::from($value);
    }

    /**
     * @param mixed ...$values
     *
     * @return array<static>
     */
    public static function coerceMany(mixed ...$values): array
    {
        return array_map(
            array: $values,
            callback: self::coerce(...),
        );
    }

    public static function tryCoerce(mixed $value): ?static
    {
        if ($value instanceof static) {
            return $value;
        }

        return static::tryFrom($value);
    }
}
