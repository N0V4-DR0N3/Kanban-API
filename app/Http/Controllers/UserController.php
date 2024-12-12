<?php

namespace App\Http\Controllers;

use App\Data\User\CreateData;
use App\Data\User\UpdateData;
use App\Exceptions\User\CannotDeleteException;
use App\Exceptions\User\CannotSelfUpdateException;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

final class UserController extends Controller
{
    readonly UserService $service;

    /**
     * @route GET /api/users
     *
     * @return AnonymousResourceCollection<UserResource>
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $users = $this->service->search(request: $request);

        return UserResource::collection($users);
    }

    /**
     * @route GET /api/users/{user}
     *
     * @throws ModelNotFoundException<User>
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * @route POST /api/users
     *
     * @throws Throwable
     */
    public function store(StoreRequest $request): UserResource
    {
        $user = $this->service->create(
            data: CreateData::fromRequest($request),
        );

        return new UserResource($user);
    }

    /**
     * @route PATCH /api/users/{user}
     *
     * @throws Throwable
     * @throws CannotSelfUpdateException
     */
    public function update(UpdateRequest $request, User $user): UserResource
    {
        if ($user->id === $this->resolveUserId()) {
            throw new CannotSelfUpdateException;
        }

        $this->service->update(
            user: $user,
            data: UpdateData::fromRequest($request),
        );

        return new UserResource($user);
    }

    /**
     * @route DELETE /api/users/{user}
     */
    public function destroy(User $user): UserResource
    {
        if ($user->id === $this->resolveUserId()) {
            throw new CannotDeleteException;
        }

        $this->service->delete(
            user: $user,
        );

        return new UserResource($user);
    }
}
