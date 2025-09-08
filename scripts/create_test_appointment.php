<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DoctorMedicaloffice;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\Appointment;
use Carbon\Carbon;

echo "Starting test appointment creation...\n";
$dp = DoctorMedicaloffice::first();
// ensure we have doctor_medicaloffice and patient available
$p = Patient::first();
if (! $dp) { echo "No DoctorMedicaloffice found.\n"; exit(1); }
if (! $p) {
    echo "No Patient found, creating one via factory...\n";
    $p = Patient::factory()->create();
    echo "Created Patient id={$p->id}\n";
}

$now = Carbon::now();
$iso = (int) $now->isoWeekday();
$weekdayStored = $iso === 7 ? 0 : $iso;

// create a schedule for today covering 08:00-18:00
$schedule = Schedule::create([
    'doctor_medicaloffice_id' => $dp->id,
    'weekdays' => (string)$weekdayStored,
    'weekday' => $weekdayStored,
    'start_time' => '08:00:00',
    'end_time' => '18:00:00',
    'turno' => null,
]);

if (! $schedule) { echo "Failed to create schedule\n"; exit(1); }

$start = $now->copy()->addHour()->startOfHour();
$end = $start->copy()->addMinutes(30);

try {
    $appt = Appointment::create([
        'patient_id' => $p->id,
        'doctor_medicaloffice_id' => $dp->id,
        'start_datetime' => $start->format('Y-m-d H:i:s'),
        'end_datetime' => $end->format('Y-m-d H:i:s'),
        'status' => 'scheduled',
        'notes' => 'Test appointment created by script',
    ]);
    echo "Appointment created: ID={$appt->id}, start={$appt->start_datetime}\n";
} catch (\Exception $e) {
    echo "Failed to create appointment: " . $e->getMessage() . "\n";
    if (method_exists($e, 'errors')) {
        print_r($e->errors());
    }
    exit(1);
}

exit(0);
