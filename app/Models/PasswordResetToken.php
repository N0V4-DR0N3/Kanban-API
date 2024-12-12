<?php

namespace App\Models;

use App\Services\PasswordResetTokenService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

/**
 * @property-read null $id
 *
 * @property string $email
 * @property string $token
 *
 * @property-read bool $expired
 *
 * @property-read Carbon $created_at
 */
class PasswordResetToken extends Model
{
    public const UPDATED_AT = null;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $guarded = [];

    /**
     * @return Attribute<bool, never>
     */
    protected function expired(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->addMinutes(PasswordResetTokenService::getExpirationMinutes())->isPast(),
        );
    }
}
