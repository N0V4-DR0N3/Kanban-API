<?php

namespace App\Enums\Concerns;

use Illuminate\Support\Collection;

trait Collectable
{
    public static function values(): Collection
    {
        return collect(self::cases())->pluck('value');
    }

    public static function arrayValues(): array
    {
        return self::values()->toArray();
    }
}
