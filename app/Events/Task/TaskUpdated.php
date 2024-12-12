<?php

namespace App\Events\Task;

use App\Models\Task;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesModels;

#[WithoutRelations]
class TaskUpdated implements ShouldDispatchAfterCommit
{
    use SerializesModels;

    public function __construct(
        public Task $task,

        /** @var array<string, mixed> */
        public array $data
    ) {
    }
}
