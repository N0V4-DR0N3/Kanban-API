<?php

namespace Database\Factories;

use App\Enums\Task\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),

            'title' => $this->faker->word(),
            'description' => $this->faker->text(),

            'limit_date' => now(),

            'status' => TaskStatus::PLANNING,

            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
