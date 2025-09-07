<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$manager = app('livewire');
// try some names
$names = ['schedules', 'Schedules', 'schedules.index', 'livewire.schedules'];
foreach ($names as $n) {
    try {
        $c = $manager->component($n);
        echo "$n => "; var_export($c); echo PHP_EOL;
    } catch (Throwable $e) {
        echo "$n => ERROR: " . $e->getMessage() . PHP_EOL;
    }
}

try {
    echo 'isDiscoverable: ' . ($manager->isDiscoverable() ? 'true' : 'false') . PHP_EOL;
} catch (Throwable $e) { echo 'isDiscoverable error: ' . $e->getMessage() . PHP_EOL; }

try {
    $resolved = $manager->resolveMissingComponent('schedules');
    echo 'resolveMissingComponent(schedules): ' . var_export($resolved, true) . PHP_EOL;
} catch (Throwable $e) { echo 'resolveMissingComponent error: ' . $e->getMessage() . PHP_EOL; }

// Show registered components by reflecting on LivewireManager internals if possible
try {
    $ref = new ReflectionClass($manager);
    echo 'Public methods on Livewire manager:' . PHP_EOL;
    foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $met) {
        echo '- ' . $met->name . PHP_EOL;
    }
} catch (Throwable $e) {
    echo 'reflection error: ' . $e->getMessage() . PHP_EOL;
}
