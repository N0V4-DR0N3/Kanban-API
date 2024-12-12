<?php

namespace App\Repositories;

use App\Data\_;
use App\Data\User\InsertData;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * @extends Repository<User>
 * @phpstan-extends Repository<User>
 */
final class UserRepository extends Repository
{
    /** @var User */
    protected Model $model;

    public function __construct()
    {
        parent::__construct(new User);
    }

    /**
     * @throws Throwable
     */
    public function create(InsertData $data): User
    {
        return $this->_create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,

            'active' => _::or($data->active, true),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(User $user, array $data): User
    {
        return $this->_update($user, $data);
    }

    /**
     * @codeCoverageIgnore
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where(compact('email'))->first();
    }

    /**
     * @codeCoverageIgnore
     */
    public function findDifferentByEmail(User $ignored, string $email): ?User
    {
        return $this->model->whereNot('id', $ignored->id)->where(compact('email'))->first();
    }

    public function delete(User $user): bool
    {
        return $this->_delete($user);
    }
}
