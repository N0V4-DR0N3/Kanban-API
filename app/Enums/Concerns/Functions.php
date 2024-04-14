<?php

namespace App\Enums\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;

/**
 * @mixin Invokable
 * @mixin Coerceable
 */
trait Functions
{
    public function is($value): bool
    {
        return $this === self::coerce($value);
    }

    public function isNot($value): bool
    {
        return !$this->is($value);
    }

    public function isAnyOf($values): bool
    {
        $values = Arr::map($values, fn ($value) => self::coerce($value));
        return in_array($this, $values);
    }

    public function isNotAnyOf($values): bool
    {
        return !$this->isAnyOf($values);
    }

    public static function values(): BaseCollection
    {
        return collect(self::cases())->pluck('value');
    }

    public static function arrayValues(): array
    {
        return self::values()->toArray();
    }
}
