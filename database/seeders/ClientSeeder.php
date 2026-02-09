<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = User::where('is_trainer', true)->get();

        foreach ($trainers as $trainer) {
            // Create 5-10 clients per trainer
            Client::factory()
                ->count(rand(5, 10))
                ->create([
                    'trainer_id' => $trainer->id,
                ]);
        }
    }
}
