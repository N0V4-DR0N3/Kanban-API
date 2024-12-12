<?php

namespace App\Data\User;

use App\Data\_;
use App\Data\Concerns\RetrievesRequestInput;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

final class CreateData extends Data
{
    use RetrievesRequestInput;

    public function __construct(
        public string $name,
        public string $email,
        public string $password,

        public _|bool $active,
    ) {
    }

    public static function fromRequest(Request $request, string $namespace = ''): self
    {
        $input = self::requestInputGetter($request, $namespace);

        return self::from([
            'name' => $input('name'),
            'email' => $input('email'),
            'password' => $input('password'),

            'active' => $input('active') ?? new _,
        ]);
    }
}
