<?php

namespace App\Data\User;

use App\Data\Concerns\RetrievesRequestInput;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

final class ResetPasswordData extends Data
{
    use RetrievesRequestInput;

    public function __construct(
        public string $token,

        public string $email,
        public string $password,
    ) {
    }

    public static function fromRequest(Request $request, string $namespace = ''): self
    {
        $input = self::requestInputGetter($request, $namespace);

        return self::from([
            'token' => $input('token'),

            'email' => $input('email'),
            'password' => $input('password'),
        ]);
    }
}
