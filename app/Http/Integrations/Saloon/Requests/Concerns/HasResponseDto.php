<?php

namespace App\Http\Integrations\Saloon\Requests\Concerns;

use App\Http\Integrations\Saloon\Data\Responses\ResponseData;
use Saloon\Http\Response;

/**
 * @template TData of ResponseData
 */
trait HasResponseDto
{
    /**
     * @return class-string<TData>
     */
    protected abstract function resolveDto(): string;

    /**
     * @param Response $response
     *
     * @return TData
     */
    public function createDtoFromResponse(Response $response): mixed
    {
        /** @var class-string<TData> $data */
        $data = $this->resolveDto();

        return $data::fromResponse($response);
    }
}
