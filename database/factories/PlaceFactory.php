<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Place;

class PlaceFactory extends Factory
{
    protected $model = Place::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address_line' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'province' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
