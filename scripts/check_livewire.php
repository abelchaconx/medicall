<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $reg = new Livewire\Mechanisms\ComponentRegistry();
    $comp = $reg->new('users');
    $r = new ReflectionClass($comp);
    echo "OK: created " . get_class($comp) . "\n";
    echo "file: " . $r->getFileName() . "\n";
    echo "namespace: " . $r->getNamespaceName() . "\n";
} catch (Throwable $e) {
    echo "ERR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
