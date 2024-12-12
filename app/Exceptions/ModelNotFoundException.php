<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException as BaseException;
use Illuminate\Support\Arr;

/**
 * @template TModel of Model
 *
 * @extends BaseException<TModel>
 * @phpstan-extends BaseException<TModel>
 */
class ModelNotFoundException extends BaseException
{
    /**
     * @param class-string<TModel> $model
     * @param ?string $message
     * @param int|string|array<int|string> $ids
     */
    public function __construct(string $model, ?string $message = null, int|string|array $ids = [])
    {
        if (!$message) {
            $message = __('exceptions.model_not_found', ['model' => class_basename($model)]);
        }

        parent::__construct($message);

        $this->model = $model;
        $this->ids = Arr::wrap($ids);
    }
}
