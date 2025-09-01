<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Doctor;
use App\Models\User;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'license_number' => $this->faker->bothify('LIC-####'),
            'bio' => $this->faker->sentence(),
        ];
    }
}
