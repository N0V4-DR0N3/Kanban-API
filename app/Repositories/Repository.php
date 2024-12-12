<?php

namespace App\Repositories;

use App\Data\_;
use App\Data\DateValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as BaseCollection;
use Throwable;

/**
 * @template TModel of Model
 */
abstract class Repository
{
    /**
     * @var TModel
     */
    protected Model $model;

    /**
     * @param TModel $model
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
        return $this->model->query();
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
     * @return TModel
     *
     * @throws Throwable
     */
    protected function _create(array $data): Model
    {
        return $this->model->create(_::filter($data));
    }

    /**
     * @param string $id
     *
     * @return ?TModel
     */
    protected function _find(string $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param string $id
     *
     * @return TModel
     *
     * @throws ModelNotFoundException
     */
    protected function _findOrFail(string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param TModel $model
     * @param array $data
     *
     * @phpstan-param array<string, mixed> $data
     *
     * @return TModel|false
     *
     * @throws Throwable
     */
    protected function _update(Model $model, array $data): Model|false
    {
        $model->updateOrFail(_::filter($data));

        return $model;
    }

    /**
     * @param TModel $model
     *
     * @return bool
     */
    protected function _delete(Model $model): bool
    {
        return !!$model->delete();
    }

    /**
     * @param TModel $model
     *
     * @return bool
     */
    protected function _forceDelete(Model $model): bool
    {
        return !!$model->forceDelete();
    }

    /**
     * @param TModel $model
     *
     * @return bool
     */
    protected function _restore(Model $model): bool
    {
        if (!method_exists($model, 'restore')) {
            return true;
        }

        return $model->restore();
    }

    /**
     * @param Collection<array-key, TModel> $models
     *
     * @return BaseCollection<int, DateValue>
     */
    protected function _reduceDateValues(Collection $models): BaseCollection
    {
        return $models->reduce(
            initial: collect(),
            callback: static function (BaseCollection $acc, Model $v, $k) {
                $date = $v->getAttribute('date');

                return $acc->put($date, DateValue::fromModel($v));
            },
        );
    }

    /**
     * @param Collection<int|string, Model> $models
     *
     * @return BaseCollection<int, DateValue>
     */
    protected function _reduceCumulativeDateValues(Collection $models): BaseCollection
    {
        return $models->reduce(
            initial: collect(),
            callback: static function (BaseCollection $acc, Model $v, $k) {
                $date = $v->getAttribute('date');
                $value = $v->getAttribute('value');

                $data = $acc->getOrPut($date, new DateValue(date: Carbon::parse($date)));
                $data->value += $value;

                return $acc;
            },
        )->values();
    }
}
