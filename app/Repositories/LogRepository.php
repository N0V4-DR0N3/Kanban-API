<?php

namespace App\Repositories;

use App\Data\Log\InsertData;
use App\Models\Log;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * @extends Repository<Log>
 * @phpstan-extends Repository<Log>
 */
final class LogRepository extends Repository
{
    /** @var Log */
    protected Model $model;

    public function __construct()
    {
        parent::__construct(new Log);
    }

    /**
     * @throws Throwable
     */
    public function create(InsertData $data): Log
    {
        return $this->_create([
            'user_id' => $data->user?->id,

            'domain' => str($data->action->value)->before('.'),
            'action' => $data->action,
            'description' => $data->description,
            'payload' => $data->payload,

            'ip' => $data->ip,
        ]);
    }
}
