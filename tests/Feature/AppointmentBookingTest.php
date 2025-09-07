<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorMedicaloffice as DoctorPlace;
use App\Models\MedicalOffice;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_prevent_duplicate_appointment_same_start()
    {
        $user = User::factory()->create();
        $patientUser = User::factory()->create();
        $doctor = Doctor::factory()->create(['user_id' => $user->id]);
        $patient = \App\Models\Patient::factory()->create(['user_id' => $patientUser->id]);
    $place = \App\Models\MedicalOffice::factory()->create();
    $dp = DoctorPlace::factory()->create(['doctor_id' => $doctor->id, 'medical_office_id' => $place->id]);

        $start = Carbon::now()->addDay()->startOfHour();
        $end = (clone $start)->addMinutes(30);

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_medicaloffice_id' => $dp->id,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'status' => 'confirmed',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Attempt to create duplicate with same start_datetime for same doctor_medicaloffice
        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_medicaloffice_id' => $dp->id,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'status' => 'pending',
        ]);
    }
}
