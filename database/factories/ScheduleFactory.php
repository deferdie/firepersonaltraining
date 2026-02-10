<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Habit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'notes' => null,
            'starts_at' => now()->addDay(),
            'ends_at' => null,
            'timezone' => 'UTC',
            'recurrence_rule' => ['mode' => 'one_off', 'interval' => 1, 'weekdays' => []],
            'recurrence_ends_at' => null,
            'is_active' => true,
        ];
    }

    public function forClientAndHabit(Client $client, Habit $habit): static
    {
        return $this->state(fn () => [
            'trainer_id' => $client->trainer_id,
            'assignable_type' => Client::class,
            'assignable_id' => $client->id,
            'schedulable_type' => Habit::class,
            'schedulable_id' => $habit->id,
        ]);
    }
}
