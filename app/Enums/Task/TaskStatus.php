<?php

namespace App\Enums\Task;

use App\Enums\Concerns\Coerceable;

enum TaskStatus: string
{
    use Coerceable;

    case PLANNING = 'planning';
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING => 'Planejamento',
            self::PENDING => 'Pendente',
            self::IN_PROGRESS => 'Em andamento',
            self::DONE => 'Concluído',
            self::ARCHIVED => 'Arquivado',
        };
    }

    public function isPlanning(): bool
    {
        return $this === self::PLANNING;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isInProgress(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    public function isDone(): bool
    {
        return $this === self::DONE;
    }

    public function isArchived(): bool
    {
        return $this === self::ARCHIVED;
    }
}
