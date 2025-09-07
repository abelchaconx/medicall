<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Consultorios médicos creados:\n\n";

$offices = DB::table('medical_offices')->select('name', 'city', 'province')->get();

foreach ($offices as $office) {
    echo $office->name . ' - ' . $office->city . ' / ' . $office->province . "\n";
}

echo "\nTotal: " . count($offices) . " consultorios\n";
