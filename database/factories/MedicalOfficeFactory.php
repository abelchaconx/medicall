<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MedicalOffice;

class MedicalOfficeFactory extends Factory
{
    protected $model = MedicalOffice::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address_line' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'province' => $this->faker->state(),
            'otros' => $this->faker->sentence(3),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
