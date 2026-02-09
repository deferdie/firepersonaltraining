<?php

namespace Database\Factories;

use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_id' => Workout::factory(),
            'name' => fake()->words(2, true),
            'sets' => fake()->numberBetween(3, 5),
            'reps' => fake()->randomElement(['8-12', '10-15', '12-15', '15-20']),
            'rest_seconds' => fake()->numberBetween(30, 120),
            'notes' => fake()->optional()->sentence(),
            'order_index' => 0,
        ];
    }
}
