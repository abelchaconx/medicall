<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\MedicalOffice;
use App\Models\DoctorMedicaloffice as DoctorPlace;
use App\Models\Specialty;

class RelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_user_and_places_and_specialties_relations()
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);

    // create a medical office
    $place = MedicalOffice::factory()->create();
    $doctorPlace = DoctorPlace::factory()->create(['doctor_id' => $doctor->id, 'medical_office_id' => $place->id]);

        $specialty = Specialty::factory()->create();
        $doctor->specialties()->attach($specialty->id);

        $this->assertTrue($user->doctor->is($doctor));
        $this->assertEquals(1, $doctor->medicalOffices()->count());
        $this->assertEquals(1, $doctor->specialties()->count());
    }
}
