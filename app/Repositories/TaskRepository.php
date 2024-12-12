<?php

namespace App\Repositories;

use App\Data\Task\InsertData;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * @extends Repository<Task>
 * @phpstan-extends Repository<Task>
 */
final class TaskRepository extends Repository
{
    /** @var Task */
    protected Model $model;

    public function __construct()
    {
        parent::__construct(new Task);
    }

    public function create(InsertData $data): Task
    {
        return $this->_create([
            'title' => $data->title,
            'description' => $data->description,

            'status' => $data->status,
            'limit_date' => $data->limit_date,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(Task $task, array $data): Task
    {
        return $this->_update(model: $task, data: $data);
    }

    public function delete(Task $task): bool
    {
        return $this->_delete(model: $task);
    }
}
