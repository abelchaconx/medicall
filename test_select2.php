<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Doctor Places API...\n";

$q = '';
$items = \App\Models\DoctorMedicaloffice::with('doctor.user', 'medicalOffice')
    ->where(function($query) use ($q) {
        if ($q) {
            $query->whereHas('doctor.user', function($q2) use ($q) {
                $q2->where('name', 'like', '%'.$q.'%');
            })->orWhereHas('medicalOffice', function($q2) use ($q) {
                $q2->where('name', 'like', '%'.$q.'%');
            });
        }
    })
    ->limit(20)
    ->get()
    ->map(function($dp) {
        $doctorName = data_get($dp, 'doctor.user.name') ?? ('Doctor #'.$dp->doctor_id);
        $placeName = data_get($dp, 'medicalOffice.name') ?? ('MedicalOffice #'.($dp->medical_office_id ?? ''));
        return ['id' => $dp->id, 'text' => $doctorName.' - '.$placeName];
    });

echo "Results found: " . $items->count() . "\n";
echo "Sample results:\n";
foreach($items->take(5) as $item) {
    echo "- ID: {$item['id']}, Text: {$item['text']}\n";
}

echo "\nTesting Patients API...\n";
$patients = \App\Models\Patient::with('user')
    ->where(function($query) use ($q) {
        if ($q) {
            $query->whereHas('user', function($q2) use ($q) {
                $q2->where('name', 'like', '%'.$q.'%');
            });
        }
    })
    ->limit(20)
    ->get()
    ->map(function($patient) {
        $userName = data_get($patient, 'user.name') ?? ('Patient #'.$patient->id);
        return ['id' => $patient->id, 'text' => $userName];
    });

echo "Patient results found: " . $patients->count() . "\n";
foreach($patients->take(3) as $patient) {
    echo "- ID: {$patient['id']}, Text: {$patient['text']}\n";
}
