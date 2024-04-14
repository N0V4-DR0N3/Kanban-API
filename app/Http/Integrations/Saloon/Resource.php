<?php

namespace App\Http\Integrations\Saloon;

use Saloon\Http\Connector;

abstract class Resource
{
    public function __construct(
        public Connector $connector,
    ) {
    }
}
