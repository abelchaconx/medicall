<?php
// Quick test for models
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $doctorMedicaloffices = \App\Models\DoctorMedicaloffice::count();
    echo "DoctorMedicaloffice count: $doctorMedicaloffices\n";
    
    if ($doctorMedicaloffices > 0) {
        $sample = \App\Models\DoctorMedicaloffice::with(['doctor.user','medicalOffice'])->first();
        $doctorName = data_get($sample, 'doctor.user.name') ?? ('Doctor #'.$sample->doctor_id);
        $placeName = data_get($sample, 'medicalOffice.name') ?? ('MedicalOffice #'.($sample->medical_office_id ?? ''));
        echo "Sample: ID {$sample->id}, Text: $doctorName - $placeName\n";
    }
    
    // Create a test user if none exists
    $users = \App\Models\User::count();
    echo "Users count: $users\n";
    
    if ($users == 0) {
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        echo "Created test user: {$user->email}\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
