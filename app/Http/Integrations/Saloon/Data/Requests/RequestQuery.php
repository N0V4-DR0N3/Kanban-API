<?php

namespace App\Http\Integrations\Saloon\Data\Requests;

use App\Http\Integrations\Saloon\Concerns\FiltersNull;
use Spatie\LaravelData\Data;

abstract class RequestQuery extends Data
{
    use FiltersNull;

    /**
     * @return array<string, string|float>
     */
    public abstract function toQuery(): array;
}
