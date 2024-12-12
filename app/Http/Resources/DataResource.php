<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelData\Data;

/**
 * @template TData of Data
 *
 * @mixin Data
 * @property-read Data $resource
 */
final class DataResource extends JsonResource
{
    /**
     * @param Data $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return $this->resource->toArray();
    }
}
