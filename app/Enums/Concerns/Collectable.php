<?php

namespace App\Enums\Concerns;

use BackedEnum;
use UnitEnum;

trait Collectable
{
    /**
     * @return list<string|int>
     */
    public static function values(): array
    {
        return array_map(
            array: static::cases(),
            callback: static fn (UnitEnum|BackedEnum $v) => $v->value ?? $v->name,
        );
    }
}
