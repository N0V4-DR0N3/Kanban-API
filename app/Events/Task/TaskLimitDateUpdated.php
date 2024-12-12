<?php

namespace App\Events\Task;

use App\Enums\Task\TaskStatus;
use App\Models\Task;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

#[WithoutRelations]
class TaskLimitDateUpdated implements ShouldDispatchAfterCommit
{
    use SerializesModels;

    public function __construct(
        public Task $task,
        public Carbon $old,
        public Carbon $new
    ) {
    }
}
