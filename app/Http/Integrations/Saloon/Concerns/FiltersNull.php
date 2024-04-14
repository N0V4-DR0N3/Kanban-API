<?php

namespace App\Http\Integrations\Saloon\Concerns;

trait FiltersNull
{
    /**
     * @param array<int|string, mixed> $arr
     *
     * @return array<string, mixed>
     */
    protected function filterNull(array $arr): array
    {
        return array_filter(
            array: $arr,
            callback: static fn ($v) => $v !== null,
        );
    }
}
