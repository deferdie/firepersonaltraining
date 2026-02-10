<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class HabitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'source_library_habit_id' => null,
        ];
    }

    public function forClient(Client $client): static
    {
        return $this->state(fn () => [
            'trainer_id' => $client->trainer_id,
            'assignable_type' => Client::class,
            'assignable_id' => $client->id,
        ]);
    }
}
