<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Livewire Event Methods\n";
echo "==============================\n\n";

// Create an instance of the Appointments component
$component = new \App\Http\Livewire\Appointments();

// Test doctorPlaceSelected method with direct ID
echo "Testing doctorPlaceSelected with ID: 1\n";
try {
    $component->doctorPlaceSelected(1);
    echo "✅ Success: doctorPlaceSelected(1) - doctor_medicaloffice_id = {$component->doctor_medicaloffice_id}\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test doctorPlaceSelected method with array format
echo "\nTesting doctorPlaceSelected with array: ['id' => 2]\n";
try {
    $component->doctorPlaceSelected(['id' => 2]);
    echo "✅ Success: doctorPlaceSelected(['id' => 2]) - doctor_medicaloffice_id = {$component->doctor_medicaloffice_id}\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test patientSelected method
echo "\nTesting patientSelected with ID: 1\n";
try {
    $component->patientSelected(1);
    echo "✅ Success: patientSelected(1) - selected_existing_patient_id = {$component->selected_existing_patient_id}\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nAll tests completed!\n";
