<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create a trainer user
        User::create([
            'name' => 'Trainer Admin',
            'email' => 'trainer@example.com',
            'password' => Hash::make('password'),
            'is_trainer' => true,
        ]);

        // Create a client user
        User::create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'is_trainer' => false,
        ]);

        // Create additional trainer users
        User::factory()->count(2)->trainer()->create();

        // Create additional client users
        User::factory()->count(5)->create();
    }
}
