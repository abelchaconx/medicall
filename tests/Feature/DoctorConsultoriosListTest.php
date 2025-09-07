<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Place;
use App\Models\MedicalOffice;
use Livewire\Livewire;

class DoctorConsultoriosListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_doctor_with_multiple_consultorios_is_listed_with_each_consultorio_present()
    {
        // Create a user and doctor
        $user = User::factory()->create(['name' => 'Dr Test']);

        $doctor = Doctor::factory()->for($user, 'user')->create(['license_number' => 'LIC-123']);

        // Create 3 medical offices and attach to doctor
        $offices = MedicalOffice::factory()->count(3)->create();
        $doctor->medicalOffices()->sync($offices->pluck('id')->toArray());

        // Render the Livewire Doctors component and assert output contains expected strings
        $component = Livewire::test(\App\Livewire\Doctors::class);

        foreach ($offices as $office) {
            $component->assertSee($office->name);
        }

        // Basic sanity: ensure doctor name appears
        $component->assertSee($user->name);
    }
}
