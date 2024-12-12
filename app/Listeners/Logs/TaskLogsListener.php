<?php

namespace App\Listeners\Logs;

use App\Events\Task\TaskCreated;
use App\Events\Task\TaskDeleted;
use App\Events\Task\TaskDescriptionUpdated;
use App\Events\Task\TaskResponsiblesUpdated;
use App\Events\Task\TaskStatusUpdated;
use App\Events\Task\TaskTitleUpdated;
use App\Events\Task\TaskUpdated;
use App\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

final class TaskLogsListener extends LogsListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function subscribe(Dispatcher $events): array
    {
        return [
            TaskCreated::class => 'handleCreated',
            TaskDeleted::class => 'handleDeleted',
            TaskDescriptionUpdated::class => 'handleDescriptionUpdated',
            TaskResponsiblesUpdated::class => 'handleResponsiblesUpdated',
            TaskStatusUpdated::class => 'handleStatusUpdated',
            TaskTitleUpdated::class => 'handleTitleUpdated',
            TaskUpdated::class => 'handleUpdated',
        ];
    }

    public function handleCreated(TaskCreated $e): void
    {
        Log::task_created($e->task);
    }

    public function handleDeleted(TaskDeleted $e): void
    {
        Log::task_deleted($e->task);
    }

    public function handleDescriptionUpdated(TaskDescriptionUpdated $e): void
    {
        Log::task_descriptionUpdated($e->task, $e->old, $e->new);
    }

    public function handleResponsiblesUpdated(TaskResponsiblesUpdated $e): void
    {
        Log::task_responsiblesUpdated($e->task, $e->old, $e->new);
    }

    public function handleStatusUpdated(TaskStatusUpdated $e): void
    {
        Log::task_statusUpdated($e->task, $e->old, $e->new);
    }

    public function handleTitleUpdated(TaskTitleUpdated $e): void
    {
        Log::task_titleUpdated($e->task, $e->old, $e->new);
    }

    public function handleUpdated(TaskUpdated $e): void
    {
        Log::task_updated($e->task, $e->data);
    }
}
