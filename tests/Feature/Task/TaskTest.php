<?php

namespace Tests\Feature\Task;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Trait\Passport;
use Laravel\Passport\Passport as BasePassport;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    use Passport;

    private ?User $user = null;
    private ?User $responsible = null;

    private array $data = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->issuePassportKeys();
        $this->issuePassportPersonalToken();

        $this->user = User::factory()->create();
        $this->responsible = User::factory()->create();

        $this->data = [
            'title' => 'Title',
            'description' => 'Description',
            'limit_date' => '2025-01-01',
            'responsibles' => [$this->responsible->id],
        ];
    }

    public function test_create_not_authenticated(): void
    {
        $response = $this->postJson('/api/tasks', $this->data);

        $response->assertUnauthorized();
    }

    public function test_create_valid_data(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->postJson('/api/tasks', $this->data);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'title', 'description', 'limit_date', 'responsibles']]);
    }

    public function test_create_title_required(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->postJson('/api/tasks', array_merge($this->data, ['title' => '']));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_create_description_opcional(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->postJson('/api/tasks', array_merge($this->data, ['description' => '']));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['description']);
    }

    public function test_create_limit_date_required(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->postJson('/api/tasks', array_merge($this->data, ['limit_date' => '']));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['limit_date']);
    }

    public function test_create_responsibles_required(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->postJson('/api/tasks', array_merge($this->data, ['responsibles' => []]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['responsibles']);
    }

    public function test_create_responsibles_invalid(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->postJson('/api/tasks', array_merge($this->data, ['responsibles' => [0]]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['responsibles.0']);
    }

    public function test_create_responsibles_not_exists(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->postJson('/api/tasks', array_merge($this->data, ['responsibles' => [999]]));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['responsibles.0']);
    }
}
