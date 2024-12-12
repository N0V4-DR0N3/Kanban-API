<?php

namespace App\Data\Task;

use App\Data\_;
use App\Enums\Task\TaskStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class InsertData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public _|TaskStatus $status,
        public Carbon $limit_date
    ) {
    }
}
