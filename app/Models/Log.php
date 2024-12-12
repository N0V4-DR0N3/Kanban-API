<?php

namespace App\Models;

use App\Enums\Log\LogAction;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read ?string $user_id
 *
 * @property-read string $domain
 * @property-read LogAction $action
 * @property string $description
 * @property object $payload
 *
 * @property string $ip
 *
 * @property-read Carbon $created_at
 * @property-read ?Carbon $updated_at
 *
 * @property-read ?User $user
 */
class Log extends Model
{
    use HasUuids;

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'user_id',
        'domain',
        'action',
        'description',
        'payload',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'action' => LogAction::class,
            'payload' => AsArrayObject::class,
        ];
    }

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
