<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => Task::STATUS_PENDING,
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_COMPLETED,
        ]);
    }
}
