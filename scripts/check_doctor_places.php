<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the framework
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Doctor;
use App\Models\MedicalOffice;
use App\Models\DoctorMedicaloffice as DoctorPlace;

echo 'doctors:' . Doctor::count() . PHP_EOL;
echo 'medical_offices:' . MedicalOffice::count() . PHP_EOL;
echo 'doctor_medicaloffices:' . DoctorPlace::count() . PHP_EOL;
 $f = DoctorPlace::with('doctor.user','medicalOffice')->first();
if ($f) {
    echo 'first_dp_id:' . $f->id . PHP_EOL;
    echo 'doctor_id:' . ($f->doctor_id ?? 'null') . PHP_EOL;
    echo 'medical_office_id:' . ($f->medical_office_id ?? 'null') . PHP_EOL;
    echo 'doctor_user:' . (optional($f->doctor->user)->name ?? 'null') . PHP_EOL;
    echo 'medical_office_name:' . (optional($f->medicalOffice)->name ?? 'null') . PHP_EOL;
} else {
    echo 'first_dp:null' . PHP_EOL;
}
