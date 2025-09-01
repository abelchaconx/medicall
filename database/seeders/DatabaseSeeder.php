<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // default test user (idempotent)
        User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        // Seed requested admin user (idempotent)
        User::updateOrCreate([
            'email' => 'abelchaconx@gmail.com',
        ], [
            'name' => 'Abel Chacon',
            'password' => Hash::make('5277032.a'),
            'status' => 'active',
        ]);

        // Add 20 additional demo users (idempotent)
        for ($i = 1; $i <= 20; $i++) {
            $email = "u{$i}@gmail.com";
            User::updateOrCreate([
                'email' => $email,
            ], [
                'name' => "Usuario {$i}",
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);
        }
    }
}
