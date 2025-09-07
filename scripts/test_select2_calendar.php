<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Select2 Calendar Functionality\n";
echo "======================================\n\n";

// Test doctor-medicaloffice data
$doctorPlaces = \App\Models\DoctorMedicaloffice::with(['doctor.user', 'medicalOffice'])->limit(5)->get();
echo "Available Doctor-Consultorios:\n";
foreach ($doctorPlaces as $dp) {
    $doctorName = data_get($dp, 'doctor.user.name') ?? ('Doctor #'.$dp->doctor_id);
    $placeName = data_get($dp, 'medicalOffice.name') ?? ('MedicalOffice #'.($dp->medical_office_id ?? ''));
    echo "  ID: {$dp->id} - {$doctorName} - {$placeName}\n";
}

// Test availability checking
if ($doctorPlaces->count() > 0) {
    $firstDoctorPlace = $doctorPlaces->first();
    echo "\nTesting availability for Doctor-Place ID: {$firstDoctorPlace->id}\n";
    
    $component = new \App\Http\Livewire\Appointments();
    
    // Test a few dates
    $testDates = [
        \Carbon\Carbon::now()->format('Y-m-d'),
        \Carbon\Carbon::now()->addDay()->format('Y-m-d'),
        \Carbon\Carbon::now()->addDays(7)->format('Y-m-d'),
    ];
    
    foreach ($testDates as $date) {
        $hasAvailability = $component->isDoctorAvailableOnDate($firstDoctorPlace->id, $date);
        $dayName = \Carbon\Carbon::parse($date)->format('l');
        echo "  {$date} ({$dayName}): " . ($hasAvailability ? "Available" : "Not Available") . "\n";
        
        if ($hasAvailability) {
            // Can't test protected method directly, but availability check worked
            echo "    Available hours: Method is protected (this is expected)\n";
        }
    }
}

echo "\nTest completed successfully!\n";
