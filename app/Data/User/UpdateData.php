<?php

namespace App\Data\User;

use App\Data\_;
use App\Data\Concerns\RetrievesRequestInput;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

final class UpdateData extends Data
{
    use RetrievesRequestInput;

    public function __construct(
        public _|string $name,
        public _|string $email,
        public _|string $password,

        public _|string|null $cpf,

        public _|bool $active,
    ) {
    }

    public static function fromRequest(Request $request, string $namespace = ''): self
    {
        $has = self::requestInputGetter($request, $namespace, 'has');
        $input = self::requestInputGetter($request, $namespace, 'input');

        return self::from([
            'name' => $input('name') ?? new _,
            'email' => $input('email') ?? new _,
            'password' => $input('password') ?? new _,

            'active' => $input('active') ?? new _,
        ]);
    }
}
