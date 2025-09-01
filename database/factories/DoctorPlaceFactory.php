<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DoctorPlace;
use App\Models\Doctor;
use App\Models\Place;

class DoctorPlaceFactory extends Factory
{
    protected $model = DoctorPlace::class;

    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'place_id' => Place::factory(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
