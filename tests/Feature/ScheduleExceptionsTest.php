<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorMedicaloffice;
use App\Models\Schedule;
use App\Models\ScheduleException;
use Livewire\Livewire;

class ScheduleExceptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_exception_for_schedule()
    {
        // Create minimal related data: user, doctor, medical office, doctor_medicaloffice
        $user = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        $office = DoctorMedicaloffice::factory()->create();

        // link doctor to office if the factory doesn't already
        $doctorOffice = DoctorMedicaloffice::create([
            'doctor_id' => $doctor->id,
            'medical_office_id' => $office->medical_office_id ?? 1,
        ]);

        // create a schedule
        $schedule = Schedule::create([
            'doctor_medicaloffice_id' => $doctorOffice->id,
            'weekday' => 1,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'duration_minutes' => 60,
            'description' => 'Test schedule',
            'turno' => null,
        ]);

        // Run Livewire component and create exception
        Livewire::test(\App\Http\Livewire\ScheduleExceptions::class)
            ->set('selectedScheduleId', $schedule->id)
            ->set('date', now()->addDay()->toDateString())
            ->set('type', 'cancel')
            ->set('start_time', '09:00')
            ->set('end_time', '09:30')
            ->set('reason', 'Test block')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('schedule_exceptions', [
            'schedule_id' => $schedule->id,
            'reason' => 'Test block',
        ]);
    }
}
