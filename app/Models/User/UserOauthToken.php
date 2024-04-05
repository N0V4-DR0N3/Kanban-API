<?php

namespace App\Models\User;

use App\Enums\Auth\AuthProviders;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $user_id
 * @property AuthProviders $provider
 * @property string $access_token
 * @property string|null $refresh_token
 * @property \Carbon\Carbon|null $expires_at
 * @property string|null $provider_id
 * @property string|null $provider_avatar
 * @property string|null $provider_discriminator
 * @property string|null $provider_username
 * @property string|null $color
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read User $user
 */
class UserOauthToken extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'provider',
        'access_token',
        'refresh_token',
        'expires_at',

        'provider_id',
        'provider_avatar',
        'provider_discriminator',
        'provider_username',

        'color',
    ];

    protected $casts = [
        'provider' => AuthProviders::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
