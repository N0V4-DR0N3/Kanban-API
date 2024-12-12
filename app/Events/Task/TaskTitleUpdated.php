<?php

namespace App\Events\Task;

use App\Models\Task;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesModels;

#[WithoutRelations]
class TaskTitleUpdated implements ShouldDispatchAfterCommit
{
    use SerializesModels;

    public function __construct(
        public Task $task,
        public string $old,
        public string $new
    ) {
    }
}
