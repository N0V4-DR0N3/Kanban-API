<?php

namespace Database\Factories\Passport;

use App\Enums\Passport\TokenName;
use App\Models\Passport\Token;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Token>
 * @phpstan-extends Factory<Token>
 */
class TokenFactory extends Factory
{
    protected $model = Token::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => fake()->uuid(),
            'client_id' => fake()->uuid(),

            'name' => fake()->randomElement(TokenName::cases()),
            'scopes' => [],

            'revoked' => false,
            'expires_at' => Carbon::tomorrow(),

            'two_factor_verified' => true,
            'two_factor_expires_at' => Carbon::tomorrow(),
        ];
    }

    public function withId(string $id): self
    {
        return $this->state(fn () => [
            'id' => $id,
        ]);
    }

    public function withUserId(string $userId): self
    {
        return $this->state(fn () => [
            'user_id' => $userId,
        ]);
    }

    public function withName(TokenName $name): self
    {
        return $this->state(fn () => [
            'name' => $name->value,
        ]);
    }

    public function revoked(): self
    {
        return $this->state(fn () => [
            'revoked' => true,
        ]);
    }

    public function expired(): self
    {
        return $this->state(fn () => [
            'expires_at' => Carbon::yesterday(),
        ]);
    }

    public function unverified(): self
    {
        return $this->state(fn () => [
            'two_factor_verified' => false,
        ]);
    }

    public function expired2FA(): self
    {
        return $this->state(fn () => [
            'two_factor_expires_at' => Carbon::yesterday(),
        ]);
    }
}
