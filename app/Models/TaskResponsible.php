<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $task_id
 * @property Task $task
 *
 * @property string $user_id
 * @property User $user
 *
 * @property-read Carbon $created_at
 * @property-read ?Carbon $updated_at
 * @property-read ?Carbon $deleted_at
 */
class TaskResponsible extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<Task>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
