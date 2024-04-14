<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

/**
 * @template TModel of Model
 */
abstract class Repository
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @return Builder<TModel>
     */
    public function query(): Builder
    {
        /** @var Builder<TModel> */
        return $this->model::query();
    }

    /**
     * @return Collection<int, TModel>
     */
    protected function _all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @param array $data
     *
     * @phpstan-param array<string, mixed> $data
     *
     * @return Model
     *
     * @throws Throwable
     */
    protected function _create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param string $id
     *
     * @return ?Model
     */
    protected function _find(string $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param string $id
     *
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    protected function _findOrFail(string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param Model $model
     * @param array $data
     *
     * @phpstan-param array<string, mixed> $data
     *
     * @return Model|false
     *
     * @throws Throwable
     */
    protected function _update(Model $model, array $data): Model|false
    {
        if (!$model->updateOrFail($data)) {
            return false;
        }

        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     *
     * @phpstan-param array<string, mixed> $data
     *
     * @return Model
     *
     * @throws Throwable
     */
    protected function _updateOrFail(Model $model, array $data): Model
    {
        $model->updateOrFail($data);

        return $model;
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    protected function _delete(Model $model): bool
    {
        return !!$model->delete();
    }

    /**
     * @param Model $model
     *
     * @return bool
     */
    protected function _forceDelete(Model $model): bool
    {
        return !!$model->forceDelete();
    }
}
