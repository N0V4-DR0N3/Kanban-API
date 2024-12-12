<?php

namespace App\Data\Task;

use App\Data\_;
use App\Data\Concerns\RetrievesRequestInput;
use App\Enums\Task\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class UpdateData extends Data
{
    use RetrievesRequestInput;

    public function __construct(
        public _|string $title,
        public _|string $description,

        public _|TaskStatus $status,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public _|Carbon $limit_date,

        public _|Collection $responsibles
    ) {
    }

    public static function fromRequest(Request $request, string $namespace = ''): self
    {
        $input = self::requestInputGetter($request, $namespace);
        $has = self::requestInputGetter($request, $namespace, 'has');

        return self::from([
            'title' => $input('title') ?? new _,
            'description' => $input('description') ?? new _,

            'status' => $input('status') ?? new _,
            'limit_date' => $input('limit_date') ?? new _,

            'responsibles' => $has('responsibles') ? collect($input('responsibles')) : new _,
        ]);
    }
}
