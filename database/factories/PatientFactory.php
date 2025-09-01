<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Patient;
use App\Models\User;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'birthdate' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
