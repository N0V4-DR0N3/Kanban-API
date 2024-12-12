<?php

namespace App\Models;

use App\Enums\Task\TaskStatus;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property-read string $id
 *
 * @property string $title
 * @property ?string $description
 *
 * @property TaskStatus $status
 * @property Carbon $limit_date
 *
 * @property TaskResponsible[]|Collection<string, TaskResponsible> $responsibles
 *
 * @property-read Carbon $created_at
 * @property-read ?Carbon $updated_at
 * @property-read ?Carbon $deleted_at
 */
class Task extends Model
{
    use HasUuids, SoftDeletes;

    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => TaskStatus::PLANNING,
    ];

    protected $fillable = [
        'title',
        'description',
        'limit_date',
        'status',
    ];

    public function casts(): array
    {
        return [
            'limit_date' => 'datetime',
            'status' => TaskStatus::class,
        ];
    }

    /**
     * @return HasMany<TaskResponsible>
     */
    public function responsibles(): HasMany
    {
        return $this->hasMany(TaskResponsible::class);
    }
}
