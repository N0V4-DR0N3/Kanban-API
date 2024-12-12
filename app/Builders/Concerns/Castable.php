<?php

namespace App\Builders\Concerns;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;

trait Castable
{
    /**
     * @param Builder|EloquentBuilder<Model>|Relation<Model> $builder
     *
     * @return static
     */
    public static function cast(Builder|EloquentBuilder|Relation $builder): static
    {
        $query = $builder;

        if ($builder instanceof EloquentBuilder) {
            $query = $builder->getQuery();
        }
        if ($builder instanceof Relation) {
            $query = $builder->getBaseQuery();
        }

        $instance = new static($query); // @phpstan-ignore-line

        if ($builder instanceof EloquentBuilder || $builder instanceof Relation) {
            $instance->setModel($builder->getModel());
        }

        return $instance;
    }
}
