<?php

namespace App\Utils;

use App\Enums\Contracts\Perm;
use App\Enums\Perm as CommonPerm;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class Perms
{
    /**
     * @return Perm[]
     */
    public static function all(): array
    {
        return collect([
            ...CommonPerm::cases(),
        ])->unique('value')->toArray();
    }

    public static function can(Perm ...$perms): bool
    {
        return Gate::check(self::values(...$perms));
    }

    public static function userCan(User $user, Perm ...$perms): bool
    {
        $user_perms = self::values(...$user->perms);
        $asked_perms = self::values(...$perms);

        return count(array_intersect($asked_perms, $user_perms)) === count($asked_perms);
    }

    public static function userCanAny(User $user, Perm ...$perms): bool
    {
        $user_perms = self::values(...$user->perms);
        $asked_perms = self::values(...$perms);

        return !!array_intersect($asked_perms, $user_perms);
    }

    public static function cannot(Perm ...$perms): bool
    {
        return !self::can(...$perms);
    }

    public static function userCannot(User $user, Perm ...$perms): bool
    {
        return !self::userCan($user, ...$perms);
    }

    /**
     * @return string[]
     */
    public static function values(Perm ...$perms): array
    {
        return array_column($perms, column_key: 'value');
    }

    public static function tryCoerce(Perm|string $value): ?Perm
    {
        return CommonPerm::tryCoerce($value);
    }

    /**
     * @return Perm[]
     */
    public static function tryCoerceMany(mixed ...$perms): array
    {
        return array_filter(array_map(self::tryCoerce(...), $perms));
    }
}
