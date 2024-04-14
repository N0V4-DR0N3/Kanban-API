<?php

namespace App\Http\Integrations\Saloon\Data\Responses\Concerns;

use Saloon\Http\Response;

trait CastableFromResponse
{
    public static function fromResponse(Response $response): static
    {
        return static::from($response->json());
    }
}
