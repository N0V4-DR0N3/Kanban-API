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
 * @phpstan-type Pair array{key: string, fn: Closure}
 *
 * @phpstan-type RawCallback Closure(TBuilder $query, Request $req): mixed
 * @phpstan-type FilledCallback Closure(TBuilder $query, mixed $v, Request $req): mixed
 * @phpstan-type StringCallback Closure(TBuilder $query, ?string $v): mixed
 * @phpstan-type NumberCallback Closure(TBuilder $query, ?float $v): mixed
 * @phpstan-type BooleanCallback Closure(TBuilder $query, mixed $v): mixed
 * @phpstan-type DateCallback Closure(TBuilder $query, mixed $v): mixed
 *
 * @phpstan-type RawPair array{key: string, fn: RawCallback}
 * @phpstan-type FilledPair array{key: string, fn: FilledCallback}
 * @phpstan-type StringPair array{key: string, fn: StringCallback}
 * @phpstan-type NumberPair array{key: string, fn: NumberCallback}
 * @phpstan-type BooleanPair array{key: string, fn: BooleanCallback}
 * @phpstan-type DatePair array{key: string, fn: DateCallback}
 */
final class RequestQueryFilter
{
    /**
     * @var array<string, StringPair>
     */
    protected array $strings = [];
    /**
     * @var array<string, NumberPair>
     */
    protected array $numbers = [];
    /**
     * @var array<string, BooleanPair>
     */
    protected array $booleans = [];
    /**
     * @var array<string, DatePair>
     */
    protected array $dates = [];

    /**
     * @var array<string, RawPair>
     */
    protected array $raws = [];
    /**
     * @var array<string, FilledPair>
     */
    protected array $wheres = [];
    /**
     * @var array<string, RawPair>
     */
    protected array $orders = [];

    /**
     * @param Request $request
     * @param Builder $query
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
     * @param Builder $query
     *
     * @return self<TCModel, TCBuilder>
     */
    public static function make(Request $request, BuilderContract $query): self
    {
        return new self($request, $query);
    }

    /**
     * @return Builder
     */
    public function apply(): BuilderContract
    {
        $pipeline = Pipeline::send($this->query);

        collect([
            $this->strings, $this->numbers, $this->booleans, $this->dates,
            $this->wheres, $this->orders, $this->raws,
        ])->map(
            $this->array2pipeable(...),
        )->each(
            $pipeline->pipe(...),
        );

        return $pipeline->thenReturn();
    }

    /**
     * @param array<string, RawPair> $arr
     * @param string $key
     * @param Closure $fn
     *
     * @return $this
     */
    protected function put(array &$arr, string $key, Closure $fn): self
    {
        $arr[$key] = compact('key', 'fn');

        return $this;
    }

    /**
     * @param array<string, FilledPair> $arr
     * @param string $key
     * @param Closure $fn
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
            $v = $v instanceof BackedEnum ? $v->value : $v;

            $query->where(static fn (BuilderContract $q) => $q
                ->where(static fn (BuilderContract $q) => $fn($query, $v, $req)),
            );
        };

        return $this->put($arr, $key, $callback);
    }

    /**
     * @param string $key
     * @param Closure $fn
     *
     * @return $this
     */
    public function raw(string $key, Closure $fn): self
    {
        return $this->put($this->raws, $key, $fn);
    }

    /**
     * @param string $key
     * @param Closure $fn
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
        $key ??= $column;

        $fn = static function (BuilderContract $query, ?string $v) use ($column, $equality) {
            $query->where($column, $equality(), $v);
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
        $key ??= $column;

        $fn = static function (BuilderContract $query, ?float $v) use ($column, $equality) {
            $query->where($column, $equality(), $v);
        };

        return $this->putFilled($this->numbers, $key, $fn);
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
        $key ??= $column;

        $fn = static function (BuilderContract $query, $v) use ($column, $equality) {
            $v = filter_var($v, FILTER_VALIDATE_BOOLEAN);

            $query->where($column, $equality(), $v);
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
        $key ??= $column;

        $fn = static function (BuilderContract $query, $v) use ($column, $equality) {
            $v = $v ? Carbon::parse($v) : null;

            $query->where($column, $equality(), $v);
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
        $fn = static function (BuilderContract $query, string $v, Request $req) use ($dirKey, $dirDefault) {
            $direction = $req->input($dirKey, $dirDefault);

            $query->orderBy($v, $direction);
        };

        return $this->putFilled($this->orders, $key, $fn);
    }

    /**
     * @param array<string, array{fn: RawCallback}> $arr
     *
     * @return Closure
     */
    protected function array2pipeable(array $arr): array
    {
        return collect($arr)->pluck('fn')->map($this->callback2pipe(...))->toArray();
    }

    /**
     * @param Closure $fn
     *
     * @return Closure(BuilderContract $query, Closure $next): mixed
     */
    protected function callback2pipe(Closure $fn): Closure
    {
        return function (BuilderContract $query, Closure $next) use ($fn) {
            $fn($query, $this->request);

            return $next($query);
        };
    }
}
