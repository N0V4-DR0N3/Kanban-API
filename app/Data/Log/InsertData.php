<?php

namespace App\Data\Log;

use App\Enums\Log\LogAction;
use App\Models\User;
use Spatie\LaravelData\Data;

final class InsertData extends Data
{
    public function __construct(
        public ?User $user,

        public LogAction $action,
        public string $description,

        /** @var array<string, mixed> */
        public array $payload = [],

        public ?string $ip = null,
    ) {
    }
}
