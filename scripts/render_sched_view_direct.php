<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Schedule;

$query = Schedule::query();
$schedules = $query->latest()->paginate(1); // small sample
$availableDoctors = \App\Models\Doctor::with('user')->get()->mapWithKeys(function($d){
    return [$d->id => optional($d->user)->name ? optional($d->user)->name : ('Doctor #' . $d->id)];
});
$availableDoctorMedicalOffices = collect();

$vars = [
    'schedules' => $schedules,
    'availableDoctorMedicalOffices' => $availableDoctorMedicalOffices,
    'availableDoctors' => $availableDoctors,
    'showForm' => false,
];

try {
    $html = view('livewire.schedules', $vars)->render();
    echo "--- HTML length: " . strlen($html) . " ---\n";
    echo substr($html,0,2000);
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
}
