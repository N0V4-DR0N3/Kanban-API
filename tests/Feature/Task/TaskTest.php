<?php

namespace Tests\Feature\Task;

use App\Enums\Task\TaskStatus;
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

    public function test_show_not_authenticated(): void
    {
        $response = $this->getJson("/api/tasks/1");

        $response->assertUnauthorized();
    }

    public function test_show_not_exists(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->getJson("/api/tasks/999");

        $response->assertNotFound();
    }

    public function test_show_valid_data(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->getJson("/api/tasks/{$task}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'title', 'description', 'limit_date', 'responsibles']]);
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

    public function test_update_not_authenticated(): void
    {
        $response = $this->patchJson("/api/tasks/1", $this->data);

        $response->assertUnauthorized();
    }

    public function test_update_not_exists(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->patchJson("/api/tasks/999", $this->data);

        $response->assertNotFound();
    }

    public function test_update_fake(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", $this->data);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'title', 'description', 'limit_date', 'responsibles']]);
    }

    public function test_update_title(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", ['title' => 'New Title']);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'New Title']);
    }

    public function test_update_description(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", ['description' => 'New Description']);

        $response->assertOk()
            ->assertJsonFragment(['description' => 'New Description']);
    }

    public function test_update_limit_date(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", ['limit_date' => '2025-01-02']);

        $response->assertOk()
            ->assertJsonFragment(['limit_date' => '2025-01-02']);
    }

    public function test_update_responsibles(): void
    {
        BasePassport::actingAs(user: $this->user);

        $responsible = User::factory()->create();

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", ['responsibles' => [$responsible->id]]);

        $response->assertOk();
    }

    public function test_update_status_to_pending(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", ['status' => TaskStatus::PENDING->value]);

        $response->assertOk()
            ->assertJsonFragment(['status' => TaskStatus::PENDING->value]);
    }

    public function test_update_status_to_in_progress(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", ['status' => TaskStatus::IN_PROGRESS->value]);

        $response->assertOk()
            ->assertJsonFragment(['status' => TaskStatus::IN_PROGRESS->value]);
    }

    public function test_update_status_to_done(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", ['status' => TaskStatus::DONE->value]);

        $response->assertOk()
            ->assertJsonFragment(['status' => TaskStatus::DONE->value]);
    }

    public function test_update_status_to_archived(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->patchJson("/api/tasks/{$task}", ['status' => TaskStatus::ARCHIVED->value]);

        $response->assertOk()
            ->assertJsonFragment(['status' => TaskStatus::ARCHIVED->value]);
    }

    public function test_delete_not_authenticated(): void
    {
        $response = $this->deleteJson("/api/tasks/1");

        $response->assertUnauthorized();
    }

    public function test_delete_not_exists(): void
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->deleteJson("/api/tasks/999");

        $response->assertNotFound();
    }

    public function test_delete_valid_data(): void
    {
        BasePassport::actingAs(user: $this->user);

        $task = $this->createTask();

        $response = $this->deleteJson("/api/tasks/{$task}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'title', 'description', 'limit_date', 'responsibles']]);
    }

    private function createTask(): string
    {
        BasePassport::actingAs(user: $this->user);

        $response = $this->postJson('/api/tasks', $this->data);

        return $response->json('data.id');
    }
}
