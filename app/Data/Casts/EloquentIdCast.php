<?php

namespace App\Data\Casts;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

final class EloquentIdCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): string|Uncastable
    {
        return $this->castValue(
            value: $value,
        );
    }

    protected function castValue(mixed $value): string|Uncastable
    {
        if ($value instanceof Model) {
            return $value->getKey();
        }
        else if (is_array($value)) {
            return $value['id'];
        }

        return Uncastable::create();
    }
}
