<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\DoctorMedicaloffice;

echo "Patients: " . Patient::count() . "\n";
echo "DoctorMedicaloffices: " . DoctorMedicaloffice::count() . "\n";
