<?php

namespace App\Enums\Concerns;

use BackedEnum;
use Error;

trait Invokable
{
    protected static function _value(self $case)
    {
        if ($case instanceof BackedEnum) {
            return $case->value;
        }

        return $case->name;
    }

    public function __invoke(): string|int
    {
        return self::_value($this);
    }

    public static function __callStatic(string $name, array $args): string|int
    {
        if ($case = collect(static::cases())->firstWhere('name', $name)) {
            return self::_value($case);
        }

        throw new Error('Unknown enum case.');
    }
}
