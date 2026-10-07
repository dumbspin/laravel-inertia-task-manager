<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(array_keys(Task::STATUSES)),
            'priority' => fake()->randomElement(array_keys(Task::PRIORITIES)),
            'due_date' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
        ];
    }
}
