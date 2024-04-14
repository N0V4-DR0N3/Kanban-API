<?php

namespace App\Http\Integrations\Saloon\Data\Responses;

use App\Http\Integrations\Saloon\Data\Responses\Concerns\CastableFromResponse;
use Spatie\LaravelData\Data;

abstract class ResponseData extends Data
{
    use CastableFromResponse;
}
