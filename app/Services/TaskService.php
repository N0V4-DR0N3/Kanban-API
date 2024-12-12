<?php

namespace App\Services;

use App\Data\_;
use App\Data\Task\CreateData;
use App\Data\Task\InsertData;
use App\Data\Task\UpdateData;
use App\Models\Task;
use App\Models\TaskResponsible;
use App\Repositories\TaskRepository;
use App\Utils\RequestQueryFilter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class TaskService extends Service
{
    readonly protected Task $model;
    readonly protected TaskRepository $repository;

    readonly protected TaskResponsible $responsibleModel;

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

            return $task;
        });
    }

    public function update(Task $task, UpdateData $data): Task
    {
        DB::transaction(function () use ($data, $task) {
            _::when($data->title, fn (string $v) => $this->setTitle($task, $v));
            _::when($data->description, fn (string $v) => $this->setDescription($task, $v));
            _::when($data->status, fn (string $v) => $this->setStatus($task, $v));
            _::when($data->limit_date, fn (string $v) => $this->setLimitDate($task, $v));
            _::when($data->responsibles, fn (array $v) => $this->setResponsibles($task, $v));

            $values = (clone $data)->except(
                'title',
                'description',
                'status', 'limit_date',
                'responsibles',
            )->toArray();
            if ($values) {
                $task = $this->repository->update(task: $task, data: $values);
            }
        });

        return $task->fresh();
    }

    public function setTitle(Task $task, string $title): Task
    {
        if (($old = $task->title) === $title) {
            return $task;
        }

        DB::transaction(function () use ($task, $title) {
            $task->title = $title;
            $task->saveOrFail();
        });

        return $task;
    }

    public function setDescription(Task $task, ?string $description)
    {
        if (($old = $task?->description) === $description) {
            return $task;
        }

        DB::transaction(function () use ($task, $description) {
            $task->description = $description;
            $task->saveOrFail();
        });

        return $task;
    }

    public function setStatus(Task $task, string $status): Task
    {
        if (($old = $task->status) === $status) {
            return $task;
        }

        DB::transaction(function () use ($task, $status) {
            $task->status = $status;
            $task->saveOrFail();
        });

        return $task;
    }

    public function setLimitDate(Task $task, string $limitDate): Task
    {
        if (($old = $task->limit_date) === $limitDate) {
            return $task;
        }

        DB::transaction(function () use ($task, $limitDate) {
            $task->limit_date = $limitDate;
            $task->saveOrFail();
        });

        return $task;
    }

    public function setResponsibles(Task $task, array $responsibles): Task
    {
        $old = $task->responsibles->pluck('id')->toArray();
        if (array_values($old) === array_values($responsibles)) {
            return $task;
        }

        DB::transaction(function () use ($task, $responsibles) {
            $task->responsibles()->sync($responsibles);
        });

        return $task;
    }

    public function delete(Task $task): Task
    {
        return DB::transaction(function () use ($task) {
            $this->repository->delete(task: $task);

            return $task;
        });
    }
}
