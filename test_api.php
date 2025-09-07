<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DoctorMedicaloffice;

// Test the API logic directly
$query = '';

echo "Testing DoctorMedicaloffice API logic...\n";

try {
    $doctorPlaces = DoctorMedicaloffice::with(['doctor.user', 'medicalOffice'])
        ->when($query, function ($q) use ($query) {
            $q->whereHas('doctor.user', function ($doctorQuery) use ($query) {
                $doctorQuery->where('name', 'like', "%{$query}%");
            })->orWhereHas('medicalOffice', function ($placeQuery) use ($query) {
                $placeQuery->where('name', 'like', "%{$query}%");
            });
        })
        ->take(10)
        ->get();

    echo "Found " . $doctorPlaces->count() . " doctor medical offices\n";

    $results = $doctorPlaces->map(function ($doctorPlace) {
        $doctorName = data_get($doctorPlace, 'doctor.user.name') ?? 'Doctor #' . $doctorPlace->doctor_id;
        $placeName = data_get($doctorPlace, 'medicalOffice.name') ?? 'Medical Office #' . $doctorPlace->medical_office_id;
        
        return [
            'id' => $doctorPlace->id,
            'text' => $doctorName . ' - ' . $placeName
        ];
    });

    echo "Sample results:\n";
    foreach ($results->take(3) as $result) {
        echo "ID: " . $result['id'] . ", Text: " . $result['text'] . "\n";
    }

    echo "\nJSON Response:\n";
    echo json_encode(['results' => $results], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
