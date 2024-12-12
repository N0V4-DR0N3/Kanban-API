<?php

namespace App\Services;

use App\Data\_;
use App\Data\User\CreateData;
use App\Data\User\InsertData;
use App\Data\User\UpdateData;
use App\Exceptions\User\CannotDeleteException;
use App\Exceptions\User\DuplicateEmailException;
use App\Models\Passport\Token;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Utils\RequestQueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @final
 */
class UserService extends Service
{
    readonly protected User $model;
    readonly protected UserRepository $repository;

    readonly protected PasswordResetTokenService $passResetService;

    /**
     * @return LengthAwarePaginator<User>
     */
    public function search(Request $request, mixed ...$filters): LengthAwarePaginator
    {
        $request = $request->mergeCloned($filters);
        $query = $this->model::query();

        RequestQueryFilter::make($request, $query)
            ->id()
            ->str('name')
            ->str('email')
            ->bool('active')
            ->dateRange('created_at', 'created_from', 'created_until')
            ->ordered()
            ->where('search', function (Builder $query, ?string $v) {
                $query
                    ->where('users.name', 'LIKE', "%{$v}%")
                    ->orWhere('users.email', 'LIKE', "%{$v}%");
            })->apply();

        return $query->latest()->paginate(
            perPage: $request->per_page ?? 20,
        );
    }

    /**
     * @throws Throwable
     */
    public function insert(InsertData $data): User
    {
        return $this->repository->create($data);
    }

    /**
     * @throws Throwable
     */
    public function create(CreateData $data): User
    {
        $checkEmail = $this->repository->findByEmail($data->email);
        throw_if($checkEmail, DuplicateEmailException::class);

        return DB::transaction(function () use ($data) {
            return $this->insert(InsertData::from($data));
        });
    }

    public function update(User $user, UpdateData $data): User
    {
        DB::transaction(function () use ($data, $user) {
            _::when($data->name, fn ($v) => $this->setName($user, $v));
            _::when($data->email, fn ($v) => $this->setEmail($user, $v));
            _::when($data->password, fn ($v) => $this->setPassword($user, $v));
            _::when($data->active, fn ($v) => $this->setActive($user, $v));

            $values = (clone $data)->except(
                'name', 'email', 'password', 'active',
            )->toArray();

            if ($values) {
                $user = $this->repository->update($user, $values);
            }
        });

        return $user;
    }

    /**
     * @throws Throwable
     */
    public function setName(User $user, string $name): User
    {
        if (($old = $user->name) === $name) {
            return $user;
        }

        DB::transaction(function () use ($user, $name, $old) {
            $user->name = $name;
            $user->saveOrFail();
        });

        return $user;
    }

    /**
     * @throws Throwable
     * @throws DuplicateEmailException
     */
    public function setEmail(User $user, string $email): User
    {
        if (($old = $user->email) === $email) {
            return $user;
        }

        $checkEmail = $this->repository->findDifferentByEmail($user, $email);
        throw_if($checkEmail, DuplicateEmailException::class);

        DB::transaction(function () use ($user, $email, $old) {
            $user->email = $email;
            $user->email_verified_at = null;
            $user->saveOrFail();
        });

        return $user;
    }

    public function setPassword(User $user, string $password): User
    {
        DB::transaction(function () use ($user, $password) {
            $user->password = $password;
            $user->saveOrFail();

            $this->revokeTokens($user);
        });

        return $user;
    }

    /**
     * @throws Throwable
     */
    public function setActive(User $user, bool $active): User
    {
        if ($user->active === $active) {
            return $user;
        }

        DB::transaction(function () use ($user, $active) {
            $user->active = $active;
            $user->saveOrFail();

            if (!$user->active) {
                $this->revokeTokens($user);
            }
        });

        return $user;
    }

    public function delete(User $user): bool
    {
        throw_if($user->isSystem(), CannotDeleteException::class);

        if ($user->deleted_at) {
            return true;
        }

        return DB::transaction(function () use ($user) {
            return $this->repository->delete($user);
        });
    }

    public function revokeTokens(User $user): User
    {
        $currentToken = request()->user()?->token();

        $user->tokens->each(static function (Token $token) use ($currentToken) {
            if ($token->getKey() === $currentToken?->getKey()) {
                return;
            }

            $token->revoke();
        });

        return $user->refresh();
    }

    /**
     * @throws Throwable
     */
    public function recoverPassword(User $user): User
    {
        DB::transaction(function () use ($user) {
            $token = $this->passResetService->create($user);
            $this->passResetService->sendRecoveryMail($user, $token->token);
        });

        return $user;
    }

    /**
     * @throws Throwable
     */
    public function resetPassword(User $user, string $token, string $password): User
    {
        DB::transaction(function () use ($user, $token, $password) {
            $passToken = $this->passResetService->find($user->email, $token);
            $tokenStatus = $this->passResetService->validate($passToken);

            $this->passResetService->assertOk($tokenStatus);
            $this->passResetService->deleteByUser($user);

            $user->password = $password;
            $user->saveOrFail();

            $this->revokeTokens($user);
        });

        return $user;
    }
}
