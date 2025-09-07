<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$office = \App\Models\MedicalOffice::with('doctors.user','doctors.specialties')->find(1);
if (! $office) { echo "NO_OFFICE"; exit(1); }
$out = [
    'id' => $office->id,
    'name' => $office->name,
    'doctors' => $office->doctors->map(function($d){
        return [
            'name' => $d->user?->name,
            'specialties' => $d->specialties->pluck('name')->toArray(),
        ];
    })->toArray(),
];
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
