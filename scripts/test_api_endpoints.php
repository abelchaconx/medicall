<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing API Endpoints\n";
echo "====================\n\n";

// Simulate the doctor-places search route
$q = '';
$items = \App\Models\DoctorMedicaloffice::with(['doctor.user','medicalOffice'])
    ->when($q, function($qbuilder) use ($q) {
        $qbuilder->whereHas('doctor.user', function($qq) use ($q){ $qq->where('name','like','%'.$q.'%'); })
            ->orWhereHas('medicalOffice', function($qq) use ($q){ $qq->where('name','like','%'.$q.'%'); });
    })->limit(10)->get()->map(function($dp){
        $doctorName = data_get($dp, 'doctor.user.name') ?? ('Doctor #'.$dp->doctor_id);
        $placeName = data_get($dp, 'medicalOffice.name') ?? ('MedicalOffice #'.($dp->medical_office_id ?? ''));
        return ['id' => $dp->id, 'text' => $doctorName.' - '.$placeName];
    });

$response = ['results' => $items->values()];

echo "Doctor-Places API Response:\n";
echo json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

// Test patients endpoint
$patients = \App\Models\Patient::with('user')->limit(5)->get()->map(function($p){
    return ['id' => $p->id, 'text' => optional($p->user)->name ? optional($p->user)->name . ' (' . optional($p->user)->email . ')' : ('Paciente #' . $p->id)];
});

echo "Patients API Response:\n";
echo json_encode(['results' => $patients->values()], JSON_PRETTY_PRINT) . "\n";
