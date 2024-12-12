<?php

namespace App\Data\User;

use App\Data\_;
use Spatie\LaravelData\Data;

final class InsertData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public _|string $password,

        public _|bool $active,
    ) {
    }

    public static function fromCreateData(CreateData $data): self
    {
        return self::from([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
 
            'active' => $data->active,
        ]);
    }
}
