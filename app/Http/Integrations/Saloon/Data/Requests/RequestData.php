<?php

namespace App\Http\Integrations\Saloon\Data\Requests;

use App\Http\Integrations\Saloon\Concerns\FiltersNull;
use Spatie\LaravelData\Data;

abstract class RequestData extends Data
{
    use FiltersNull;

    /**
     * @return array<string, mixed>
     */
    public abstract function toBody(): array;
}
