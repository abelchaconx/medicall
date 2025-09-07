<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// minimal data to render view
$doctors = App\Models\Doctor::with(['user','specialties','medicalOffices'])->limit(1)->get();
// Create a LengthAwarePaginator-like object to satisfy the view
$paginator = new Illuminate\Pagination\LengthAwarePaginator($doctors, 1, 12);

$vars = [
	'doctors' => $paginator,
	'availableUsers' => [],
	'availableSpecialties' => [],
	'availableMedicalOffices' => [],
	'trashedCount' => 0,
	'showForm' => false,
];

echo view('livewire.doctors', $vars)->render();
