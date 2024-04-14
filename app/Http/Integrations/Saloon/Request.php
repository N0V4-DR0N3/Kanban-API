<?php

namespace App\Http\Integrations\Saloon;

use App\Http\Integrations\Saloon\Concerns\FiltersNull;
use Saloon\Http\Request as BaseRequest;

abstract class Request extends BaseRequest
{
    use FiltersNull;

    protected function url(string ...$parts): string
    {
        return implode('/', $parts);
    }
}
