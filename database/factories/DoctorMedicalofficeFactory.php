<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DoctorMedicaloffice as DoctorPlace;
use App\Models\Doctor;
use App\Models\MedicalOffice;

class DoctorMedicalofficeFactory extends Factory
{
    protected $model = DoctorPlace::class;

    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'medical_office_id' => MedicalOffice::factory(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
