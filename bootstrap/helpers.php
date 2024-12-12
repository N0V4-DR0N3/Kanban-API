<?php

use App\Builders\Concerns\Castable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\SerializableClosure\Support\ReflectionClosure;

/**
 * @template TModel of Model
 * @template TBuilder of Builder<TModel>
 * @template TReturn
 *
 * @param Closure(TBuilder $builder, ...$args): TReturn $fn
 *
 * @return Closure(Builder<Model> $builder, ...$args): TReturn
 *
 * @throws Exception
 */
function withBuilder(Closure $fn): callable
{
    $ref = new ReflectionClosure($fn);

    $params = $ref->getParameters();

    if (!$params) {
        throw new Exception('The callable supplied to [withBuilder] expects the first parameter to be of type ['.Builder::class.'].');
    }

    $builderParam = $ref->getParameters()[0]->getType();

    if (!$builderParam instanceof ReflectionNamedType) {
        throw new Exception('The callable supplied to [withBuilder] expects the first parameter to be of type ['.Builder::class.']. Unions and intersections are not allowed.');
    }

    $builderClass = $builderParam->getName();

    if (!class_exists($builderClass)) {
        throw new Exception('Trying to inject a Builder without specifying its type.');
    }
    if (!is_subclass_of($builderClass, Builder::class)) {
        throw new Exception("Trying to inject a Builder which doesn't extend [".Builder::class.'].');
    }
    if (!in_array(Castable::class, class_uses($builderClass))) {
        throw new Exception("Trying to inject a Builder which doesn't use [".Castable::class.'].');
    }

    return static function (Builder $query, ...$args) use ($fn, $builderClass) {
        /** @var TBuilder $builder */
        $builder = [$builderClass, 'cast']($query);

        return $fn($builder, ...$args);
    };
}

function mask(string $value, string $mask): string
{
    $masked = '';
    $k = 0;

    for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
        $char = $mask[$i] ?? '';

        if ($char === '#') {
            if (isset($value[$k])) {
                $masked .= $value[$k++];
            }
        }
        else if ($char) {
            $masked .= $char;
        }
    }

    return $masked;
}
