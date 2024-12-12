<?php

namespace App\Data\Task;

use App\Data\_;
use App\Data\Concerns\RetrievesRequestInput;
use App\Enums\Task\TaskStatus;
use Carbon\CarbonImmutable;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class CreateData extends Data
{
    use RetrievesRequestInput;

    public function __construct(
        public string $title,
        public string $description,

        public _|TaskStatus $status,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $limit_date,

        public Collection $responsibles
    ) {
    }

    public static function fromRequest(Request $request, string $namespace = ''): self
    {
        $input = self::requestInputGetter($request, $namespace);

        return self::from([
            'title' => $input('title'),
            'description' => $input('description'),

            'status' => $input('status') ?? new _,
            'limit_date' => $input('limit_date'),

            'responsibles' => collect($input('responsibles')),
        ]);
    }
}
