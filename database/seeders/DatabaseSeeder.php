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

        // Seed medical offices
        $this->call([
            MedicalOfficeSeeder::class,
        ]);

    // Seed some specialties (curated list)
    $this->call([SpecialtySeeder::class]);

        // Create example doctors and attach specialty + one or more medical offices
        \App\Models\Doctor::factory()->count(10)->create()->each(function($doctor){
            $specialty = \App\Models\Specialty::inRandomOrder()->first();
            if ($specialty) $doctor->specialties()->sync([$specialty->id]);

            $officeIds = \App\Models\MedicalOffice::inRandomOrder()->take(rand(1,3))->pluck('id')->toArray();
            if (!empty($officeIds)) $doctor->medicalOffices()->sync($officeIds);
        });
    }
}
