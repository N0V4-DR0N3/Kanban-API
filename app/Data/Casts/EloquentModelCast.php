<?php

namespace App\Data\Casts;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Types\NamedType;

final class EloquentModelCast implements Cast
{
    public function __construct(
        protected ?string $model = null,
        protected ?bool $nullable = null,
    ) {
    }

    /**
     * @throws Exception
     * @throws ModelNotFoundException
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): Model|null|Uncastable
    {
        return $this->castValue(
            class: $this->model ?? $this->inferPropertyClass($property),
            value: $value,
            nullable: $this->nullable ?? $this->inferPropertyNullable($property),
        );
    }

    /**
     * @throws Exception
     * @throws ModelNotFoundException
     */
    protected function castValue(string $class, mixed $value, bool $nullable): Model|null|Uncastable
    {
        $model = $this->instantiateModel($class);

        if ($value instanceof $model) {
            return $value;
        }
        if ($id = (string) $value) {
            if ($nullable) {
                return $model::query()->find($id);
            }

            return $model::query()->findOrFail($id);
        }

        return Uncastable::create();
    }

    /**
     * @throws Exception
     */
    protected function inferPropertyClass(DataProperty $property): string
    {
        $type = $property->type->type;

        if ($type instanceof NamedType) {
            $class = $type->name;
        }
        else {
            throw new Exception('[EloquentModelCast] failed: the type of the property being cast is too wide.');
        }

        if (!class_exists($class)) {
            throw new Exception('[EloquentModelCast] failed: the type of the property being cast is not a class.');
        }

        return $class;
    }

    protected function inferPropertyNullable(DataProperty $property): bool
    {
        return $property->type->isNullable;
    }

    /**
     * @throws Exception
     */
    protected function instantiateModel(string $class): Model
    {
        $instance = new $class;

        if (!$instance instanceof Model) {
            throw new Exception('[EloquentModelCast] failed: the type of the property being cast is not an Eloquent model.');
        }

        return $instance;
    }
}
