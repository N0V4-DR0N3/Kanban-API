<?php

namespace App\Utils;

use App\Enums\MathEquality;
use BackedEnum;
use Closure;
use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Pipeline;

/**
 * @template TModel of Model
 * @template TBuilder of Builder<TModel>|Relation<TModel>
 *
 * @phpstan-type RawCallback Closure(TBuilder $query, Request $req): mixed
 * @phpstan-type FilledCallback Closure(TBuilder $query, mixed $v, Request $req): mixed
 *
 * @phpstan-type RawPair array{key: string, fn: RawCallback}
 *
 * @phpstan-type RawPairCollection array<string, RawPair>
 */
final class RequestQueryFilter
{
    /**
     * @var RawPairCollection
     */
    protected array $strings = [];
    /**
     * @var RawPairCollection
     */
    protected array $numbers = [];
    /**
     * @var RawPairCollection
     */
    protected array $booleans = [];
    /**
     * @var RawPairCollection
     */
    protected array $dates = [];

    /**
     * @var RawPairCollection
     */
    protected array $raws = [];
    /**
     * @var RawPairCollection
     */
    protected array $wheres = [];
    /**
     * @var RawPairCollection
     */
    protected array $orders = [];

    /**
     * @param Request $request
     * @param TBuilder $query
     */
    function __construct(
        protected Request $request,
        protected BuilderContract $query
    ) {
    }

    /**
     * @template TCModel of Model
     * @template TCBuilder of Builder<TCModel>|Relation<TCModel>
     *
     * @param Request $request
     * @param TCBuilder $query
     *
     * @return self<TCModel, TCBuilder>
     */
    public static function make(Request $request, BuilderContract $query): self
    {
        return new self($request, $query);
    }

    /**
     * @return TBuilder
     */
    public function apply(): BuilderContract
    {
        $this->query->where(function (BuilderContract $query) {
            $this->pipeCollections(
                query: $query,
                scoped: true,
                collections: [$this->strings, $this->numbers, $this->booleans, $this->dates, $this->wheres],
            );
        });

        $this->pipeCollections(
            query: $this->query,
            scoped: false,
            collections: [$this->orders, $this->raws],
        );

        return $this->query;
    }

    /**
     * @param RawPairCollection $arr
     * @param string $key
     * @param RawCallback $fn
     *
     * @return $this
     */
    protected function put(array &$arr, string $key, Closure $fn): self
    {
        $arr[$key] = compact('key', 'fn');

        return $this;
    }

    /**
     * @param RawPairCollection $arr
     * @param string $key
     * @param FilledCallback $fn
     *
     * @return $this
     */
    protected function putFilled(array &$arr, string $key, Closure $fn): self
    {
        $callback = static function (BuilderContract $query, Request $req) use ($key, $fn) {
            if (!$req->has($key)) {
                return;
            }

            $v = $req->input($key);
            $v instanceof BackedEnum && ($v = $v->value);

            $fn($query, $v, $req);
        };

        return $this->put($arr, $key, $callback);
    }

    /**
     * @param string $key
     * @param RawCallback $fn
     *
     * @return $this
     */
    public function raw(string $key, Closure $fn): self
    {
        return $this->put($this->raws, $key, $fn);
    }

    /**
     * @param string $key
     * @param FilledCallback $fn
     *
     * @return $this
     */
    public function where(string $key, Closure $fn): self
    {
        return $this->putFilled($this->wheres, $key, $fn);
    }

    /**
     * @param string $column
     * @param ?string $key
     * @param MathEquality $equality
     *
     * @phpstan-param model-property<TModel> $column
     *
     * @return $this
     */
    public function str(string $column, ?string $key = null, MathEquality $equality = MathEquality::EQ): self
    {
        $key ??= str($column)->afterLast('.')->toString();

        $fn = static function (BuilderContract $query, ?string $v) use ($column, $equality) {
            $query->where($column, $equality->value, $v);
        };

        return $this->putFilled($this->strings, $key, $fn);
    }

    /**
     * @param string $column
     * @param ?string $key
     * @param MathEquality $equality
     *
     * @phpstan-param model-property<TModel> $column
     *
     * @return $this
     */
    public function num(string $column, ?string $key = null, MathEquality $equality = MathEquality::EQ): self
    {
        $key ??= str($column)->afterLast('.')->toString();

        $fn = static function (BuilderContract $query, ?float $v) use ($column, $equality) {
            $query->where($column, $equality->value, $v);
        };

        return $this->putFilled($this->numbers, $key, $fn);
    }

    /**
     * @param string $column
     * @param string $minKey
     * @param string $maxKey
     *
     * @phpstan-param model-property<TModel> $column
     *
     * @return $this
     */
    public function numRange(string $column, string $minKey, string $maxKey): self
    {
        return $this
            ->num($column, $minKey, MathEquality::GTE)
            ->num($column, $maxKey, MathEquality::LTE);
    }

    /**
     * @param string $column
     * @param ?string $key
     * @param MathEquality $equality
     *
     * @phpstan-param model-property<TModel> $column
     *
     * @return $this
     */
    public function bool(string $column, ?string $key = null, MathEquality $equality = MathEquality::EQ): self
    {
        $key ??= str($column)->afterLast('.')->toString();

        $fn = static function (BuilderContract $query, $v) use ($column, $equality) {
            $v = filter_var($v, FILTER_VALIDATE_BOOLEAN);

            $query->where($column, $equality->value, $v);
        };

        return $this->putFilled($this->booleans, $key, $fn);
    }

    /**
     * @param string $column
     * @param ?string $key
     * @param MathEquality $equality
     *
     * @phpstan-param model-property<TModel> $column
     *
     * @return $this
     */
    public function date(string $column, ?string $key = null, MathEquality $equality = MathEquality::EQ): self
    {
        $key ??= str($column)->afterLast('.')->toString();

        $fn = static function (BuilderContract $query, $v) use ($column, $equality) {
            $v = $v ? Carbon::parse($v) : null;

            $query->where($column, $equality->value, $v);
        };

        return $this->putFilled($this->dates, $key, $fn);
    }

    /**
     * @param string $column
     * @param string $fromKey
     * @param string $untilKey
     *
     * @phpstan-param model-property<TModel> $column
     *
     * @return $this
     */
    public function dateRange(string $column, string $fromKey, string $untilKey): self
    {
        return $this
            ->date($column, $fromKey, MathEquality::GTE)
            ->date($column, $untilKey, MathEquality::LTE);
    }

    /**
     * @param string $key
     * @param string $dirKey
     * @param string $dirDefault
     *
     * @return $this
     */
    public function ordered(string $key = 'order_by', string $dirKey = 'order_direction', string $dirDefault = 'asc'): self
    {
        $model = $this->query->getModel();

        $searchable = [
            ...$model->getFillable(),
            $model->getCreatedAtColumn(), $model->getUpdatedAtColumn(),
            method_exists($model, 'getDeletedAtColumn') ? $model->getDeletedAtColumn() : '',
        ];

        $fn = static function (BuilderContract $query, string $v, Request $req) use ($searchable, $dirKey, $dirDefault) {
            if (!in_array($v, $searchable)) {
                return;
            }

            $direction = $req->input($dirKey, $dirDefault);

            $query->orderBy($v, $direction);
        };

        return $this->putFilled($this->orders, $key, $fn);
    }

    /**
     * @return $this
     */
    public function strCommas(string $column, ?string $key = null): self
    {
        $key ??= str($column)->afterLast('.')->toString();

        return $this->where($key, function (Builder $query, ?string $v) use ($column) {
            $values = explode(',', $v ?? '');

            if (count($values) > 1) {
                $query->whereIn($column, $values);
            }
            else if ($id = $values[0]) {
                $query->where($column, $id);
            }
        });
    }

    /**
     * @return $this
     */
    public function id(?string $column = null, ?string $key = null): self
    {
        $column ??= $this->query->getModel()->getQualifiedKeyName();
        $key ??= str($column)->afterLast('.')->toString();

        return $this->strCommas($column, $key);
    }

    /**
     * @return $this
     */
    public function enum(string $column, ?string $key = null): self
    {
        return $this->strCommas($column, $key);
    }

    /**
     * @param array<string, array{fn: RawCallback}> $arr
     * @param bool $scoped
     *
     * @return list<RawCallback>
     */
    protected function array2pipeable(array $arr, bool $scoped): array
    {
        return collect(
            value: $arr,
        )->pluck(
            value: 'fn',
        )->map(
            callback: fn (Closure $v) => $this->callback2pipeable($v, scoped: $scoped),
        )->toArray();
    }

    /**
     * @param RawCallback $fn
     * @param bool $scoped
     *
     * @return Closure(BuilderContract $query, Closure $next): mixed
     */
    protected function callback2pipeable(Closure $fn, bool $scoped): Closure
    {
        return function (BuilderContract $query, Closure $next) use ($fn, $scoped) {
            !$scoped
                ? $fn($query, $this->request)
                : $query->where(fn (BuilderContract $query) => $fn($query, $this->request));

            return $next($query);
        };
    }

    /**
     * @param BuilderContract $query
     * @param bool $scoped
     * @param list<RawPairCollection> $collections
     *
     * @return BuilderContract
     */
    protected function pipeCollections(BuilderContract $query, bool $scoped, iterable $collections): BuilderContract
    {
        $pipeline = Pipeline::send($query);

        collect(
            value: $collections,
        )->map(
            callback: fn (array $v) => $this->array2pipeable($v, scoped: $scoped),
        )->each(
            callback: $pipeline->pipe(...),
        );

        return $pipeline->thenReturn();
    }
}
