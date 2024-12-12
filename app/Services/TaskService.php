<?php

namespace App\Services;

use App\Data\_;
use App\Data\Task\CreateData;
use App\Data\Task\InsertData;
use App\Data\Task\UpdateData;
use App\Enums\Task\TaskStatus;
use App\Events\Task\TaskCreated;
use App\Events\Task\TaskDeleted;
use App\Events\Task\TaskDescriptionUpdated;
use App\Events\Task\TaskLimitDateUpdated;
use App\Events\Task\TaskStatusUpdated;
use App\Events\Task\TaskTitleUpdated;
use App\Events\Task\TaskUpdated;
use App\Models\Task;
use App\Models\TaskResponsible;
use App\Repositories\TaskRepository;
use App\Utils\RequestQueryFilter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class TaskService extends Service
{
    readonly protected Task $model;
    readonly protected TaskRepository $repository;

    public function search(Request $request, mixed ...$filters): LengthAwarePaginator
    {
        $request = $request->mergeCloned($filters);
        $query = $this->repository->query();

        RequestQueryFilter::make(request: $request, query: $query)
            ->id()
            ->str('title')
            ->str('description')
            ->enum('status')
            ->date('limit_date')
            ->ordered()
            ->where('search', function ($query, $v) {
                $query->where('tasks.title', 'LIKE', "%{$v}%");
            })->apply();

        return $query->paginate(
            perPage: $request->per_page ?? 20,
        );
    }

    public function create(CreateData $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task = $this->repository->create(InsertData::from($data));

            $responsibles = $data->responsibles->map(fn (string $id) => ['user_id' => $id]);
            $task->responsibles()->createMany($responsibles);

            event(new TaskCreated($task));

            return $task;
        });
    }

    public function update(Task $task, UpdateData $data): Task
    {
        DB::transaction(function () use ($data, $task) {
            _::when($data->title, fn (string $v) => $this->setTitle($task, $v));
            _::when($data->description, fn (string $v) => $this->setDescription($task, $v));
            _::when($data->status, fn (TaskStatus $v) => $this->setStatus($task, $v));
            _::when($data->limit_date, fn (Carbon $v) => $this->setLimitDate($task, $v));
            _::when($data->responsibles, fn (Collection $v) => $this->setResponsibles($task, $v));

            $values = (clone $data)->except(
                'title',
                'description',
                'status', 'limit_date',
                'responsibles',
            )->toArray();
            if ($values) {
                $task = $this->repository->update(task: $task, data: $values);

                event(new TaskUpdated($task, $values));
            }
        });

        return $task->fresh();
    }

    public function setTitle(Task $task, string $title): Task
    {
        if (($old = $task->title) === $title) {
            return $task;
        }

        DB::transaction(function () use ($task, $title, $old) {
            $task->title = $title;
            $task->saveOrFail();

            event(new TaskTitleUpdated($task, $old, $title));
        });

        return $task;
    }

    public function setDescription(Task $task, ?string $description)
    {
        if (($old = $task?->description) === $description) {
            return $task;
        }

        DB::transaction(function () use ($task, $description, $old) {
            $task->description = $description;
            $task->saveOrFail();

            event(new TaskDescriptionUpdated($task, $old, $description));
        });

        return $task;
    }

    public function setStatus(Task $task, TaskStatus $status): Task
    {
        if (($old = $task->status) === $status) {
            return $task;
        }

        DB::transaction(function () use ($task, $status, $old) {
            $task->status = $status;
            $task->saveOrFail();

            event(new TaskStatusUpdated($task, $old, $status));
        });

        return $task;
    }

    public function setLimitDate(Task $task, Carbon $limitDate): Task
    {
        if (($old = $task->limit_date) === $limitDate) {
            return $task;
        }

        DB::transaction(function () use ($task, $limitDate, $old) {
            $task->limit_date = $limitDate;
            $task->saveOrFail();

            event(new TaskLimitDateUpdated($task, $old, $limitDate));
        });

        return $task;
    }

    public function setResponsibles(Task $task, Collection $responsibles): Task
    {
        $old = $task->responsibles->pluck('id');
        if ($old->count() === $responsibles->count() && $old->diff($responsibles)->isEmpty()) {
            return $task;
        }

        DB::transaction(function () use ($task, $responsibles) {
            $task->responsibles()->delete();
            $task->responsibles()->createMany($responsibles->map(fn (string $id) => ['user_id' => $id]));
        });

        return $task;
    }

    public function delete(Task $task): Task
    {
        return DB::transaction(function () use ($task) {
            $this->repository->delete(task: $task);

            event(new TaskDeleted($task));

            return $task;
        });
    }
}
