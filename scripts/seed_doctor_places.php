<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Doctor;
use App\Models\MedicalOffice;
use App\Models\DoctorMedicaloffice as DoctorPlace;

$doctors = Doctor::all();
$place = MedicalOffice::first();
if (!$place) {
    echo "No medical offices found. Create one and re-run this script.\n";
    exit(1);
}

$created = 0;
foreach ($doctors as $d) {
    $exists = DoctorPlace::where('doctor_id', $d->id)->exists();
    if (!$exists) {
    DoctorPlace::create(['doctor_id' => $d->id, 'medical_office_id' => $place->id]);
    $created++;
    echo "Created DoctorMedicaloffice for doctor_id={$d->id} -> medical_office_id={$place->id}\n";
    } else {
        echo "Doctor {$d->id} already has a DoctorMedicaloffice, skipping.\n";
    }
}

echo "Done. Created: {$created}\n";
