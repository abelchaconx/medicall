<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use App\Http\Livewire\Schedules;
use App\Models\Doctor;
use App\Models\MedicalOffice;
use App\Models\DoctorMedicaloffice as DoctorPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchedulesComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_starts_with_no_doctor_or_consultorio_selected()
    {
        // seed minimal data
        Doctor::factory()->count(1)->create();

        Livewire::test(Schedules::class)
            ->call('create')
            ->assertSet('doctor_id', null)
            ->assertSet('doctor_medicaloffice_id', null);
    }

    public function test_selecting_doctor_updates_consultorios_list_and_enables_select()
    {
        $doctor = Doctor::factory()->create();
        $office1 = MedicalOffice::factory()->create(['name' => 'Office A']);
        $office2 = MedicalOffice::factory()->create(['name' => 'Office B']);
        $dp1 = DoctorPlace::factory()->create(['doctor_id' => $doctor->id, 'medical_office_id' => $office1->id]);
        $dp2 = DoctorPlace::factory()->create(['doctor_id' => $doctor->id, 'medical_office_id' => $office2->id]);

        Livewire::test(Schedules::class)
            ->set('doctor_id', $doctor->id)
            ->assertSee('Office A')
            ->assertSee('Office B');
    }

    public function test_saving_with_consultorio_not_belonging_to_doctor_fails()
    {
        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();
        $office = MedicalOffice::factory()->create();
        $dpForOther = DoctorPlace::factory()->create(['doctor_id' => $otherDoctor->id, 'medical_office_id' => $office->id]);

        Livewire::test(Schedules::class)
            ->set('doctor_id', $doctor->id)
            ->set('doctor_medicaloffice_id', $dpForOther->id)
            ->set('description', 'Test')
            ->call('save')
            ->assertHasErrors(['doctor_medicaloffice_id']);
    }
}
