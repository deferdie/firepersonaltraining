<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trainer_id' => User::factory()->trainer(),
            'name' => fake()->words(3, true) . ' Program',
            'description' => fake()->paragraph(),
            'weeks' => fake()->numberBetween(4, 16),
        ];
    }
}
