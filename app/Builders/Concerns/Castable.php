<?php

namespace App\Builders\Concerns;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

trait Castable
{
    /**
     * @param Builder|EloquentBuilder<Model> $builder
     *
     * @return static
     */
    public static function from(Builder|EloquentBuilder $builder): static
    {
        $query = $builder;

        if ($builder instanceof EloquentBuilder) {
            $query = $builder->getQuery();
        }

        $instance = new static($query); // @phpstan-ignore-line

        if ($builder instanceof EloquentBuilder) {
            $instance->setModel($builder->getModel());
        }

        return $instance;
    }
}
