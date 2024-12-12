<?php

namespace App\Http\Controllers;

use App\Data\Task\CreateData;
use App\Data\Task\UpdateData;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    readonly protected TaskService $service;

    /**
     * @route GET /api/tasks
     *
     * @return AnonymousResourceCollection<TaskResource>
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $tasks = $this->service->search(request: $request);

        return TaskResource::collection($tasks);
    }

    /**
     * @route GET /api/tasks/{task}
     *
     * @return TaskResource
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    /**
     * @route POST /api/tasks
     *
     * @return TaskResource
     */
    public function store(StoreRequest $request): TaskResource
    {
        $task = $this->service->create(CreateData::fromRequest(request: $request));

        return new TaskResource($task);
    }

    /**
     * @route PATCH /api/tasks/{task}
     *
     * @return TaskResource
     */
    public function update(UpdateRequest $request, Task $task)
    {
        $this->service->update(task: $task, data: UpdateData::fromRequest(request: $request));

        return new TaskResource($task);
    }

    /**
     * @route DELETE /api/tasks/{task}
     *
     * @return TaskResource
     */
    public function destroy(Task $task): TaskResource
    {
        $this->service->delete(task: $task);

        return new TaskResource($task);
    }
}
