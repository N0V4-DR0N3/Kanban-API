<?php

namespace Auth;

use App\Exceptions\User\UserInactiveException;
use App\Mail\Mail;
use App\Models\Passport\Token;
use App\Models\User;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Trait\Passport;
use Laravel\Passport\Passport as BasePassport;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use Passport;

    private ?User $user = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->issuePassportKeys();
        $this->issuePassportPersonalToken();

        $this->user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'dev@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_get_device_not_authenticated(): void
    {
        $response = $this->getJson('/api/auth');

        $response->assertStatus(401);
    }

    public function test_login_valid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token', 'device_id']]);
    }

    public function test_login_email_invalid(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonPath('errors.email.0', __('validation.email', ['attribute' => 'email']));
    }

    public function test_login_email_not_found(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'not-found@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonPath('errors.email.0', __('rules.email', ['attribute' => 'email']));
    }

    public function test_login_password_invalid(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'invalid',
        ]);

        $response->assertStatus(403);
    }

    public function test_login_short_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'pass',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJsonPath('errors.password.0', __('validation.min.string', ['attribute' => 'password', 'min' => 6]));
    }

    public function test_login_attempts_exceeded(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => $this->user->email,
                'password' => 'invalid',
            ]);
        }

        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'invalid',
        ]);
        $response->assertStatus(429)
            ->assertJson(['message' => 'Too Many Attempts.']);
    }

    public function test_logout_success(): void
    {
        BasePassport::actingAs(user: $this->user);
        $this->user->withAccessToken(Token::factory()->create());
        $old = $this->user->token();

        $response = $this->postJson('/api/auth/logout');

        $response->assertNoContent();

        $this->assertGuest();
        $this->assertDatabaseHas('oauth_access_tokens', [
            'id' => $old->id,
            'revoked' => true,
        ]);
    }

    public function test_logout_not_authenticated(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    #region [TODO: Recover password]
    public function test_recover_password_valid_email(): void
    {
        $response = $this->postJson('/api/auth/recover-password', [
            'email' => $this->user->email,
        ]);

        $response->assertNoContent();
    }

    public function test_recover_password_email_invalid(): void
    {
        $response = $this->postJson('/api/auth/recover-password', [
            'email' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonPath('errors.email.0', __('validation.email', ['attribute' => 'email']));
    }

    public function test_recover_password_email_not_found(): void
    {
        $response = $this->postJson('/api/auth/recover-password', [
            'email' => 'notfound@test.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJsonPath('errors.email.0', __('validation.exists', ['attribute' => 'email']));
    }

    public function test_recover_password_email_inactive(): void
    {
        $user = User::factory()->create([
            'email' => 'testrecover@test.com',
            'active' => false,
        ]);

        $response = $this->postJson('/api/auth/recover-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => __('exceptions.user.inactive')]);
    }

    public function test_recover_email_send(): void
    {
        Event::fake();

        $response = $this->postJson('/api/auth/recover-password', [
            'email' => $this->user->email,
        ]);

        $response->assertNoContent();
    }
    #endregion
}
