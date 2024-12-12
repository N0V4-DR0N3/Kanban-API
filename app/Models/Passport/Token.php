<?php

namespace App\Models\Passport;

use App\Models\Concerns\MockableSave;
use App\Models\User;
use Database\Factories\Passport\TokenFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Laravel\Passport\Client;
use Laravel\Passport\Token as BaseToken;

/**
 * @mixin Builder<self>
 *
 * @property-read string $id
 * @property-read string $client_id
 * @property-read string $user_id
 *
 * @property-read string $name
 * @property-read array $scopes
 *
 * @property-read bool $revoked
 * @property-read Carbon $expires_at
 *
 * @property bool $two_factor_verified
 * @property ?Carbon $two_factor_expires_at
 *
 * @property-read Client $client
 * @property-read User $user
 */
class Token extends BaseToken
{
    use HasUuids;
    use MockableSave;

    /** @use HasFactory<TokenFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'scopes' => 'array',
            'revoked' => 'bool',
            'two_factor_verified' => 'bool',
            'expires_at' => 'datetime',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
