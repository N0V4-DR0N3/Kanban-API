<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\RoleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @phpstan-type UserData array{id: string, name: string, email: string, password: string, role: string}
 */
final class SeedUsers extends Seeder
{
    readonly protected RoleService $roleService;

    /**
     * @return list<UserData>
     */
    protected function users(): array
    {
        return [
            [
                'id' => 'c9b7556b-eb36-445a-9684-8de694dd6c41',
                'name' => 'System',
                'email' => 'system@kb.dev',
                'password' => 'System_1234',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            collect(
                $this->users(),
            )->each(function (array $values) {
                extract($values, EXTR_OVERWRITE);

                $password = Hash::make($password);
                $role = $this->roleService->findByName($role);

                /** @var User $user */
                $user = User::query()->updateOrCreate(
                    attributes: compact('id'),
                    values: compact('name', 'email', 'password'),
                );

                $user->syncRoles($role);
            });
        });
    }
}
