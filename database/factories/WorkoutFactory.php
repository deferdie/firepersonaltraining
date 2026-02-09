<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkoutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trainer_id' => User::factory()->trainer(),
            'program_id' => Program::factory(),
            'name' => fake()->words(2, true) . ' Workout',
            'description' => fake()->sentence(),
        ];
    }
}
