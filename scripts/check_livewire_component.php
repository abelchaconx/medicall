<?php
require __DIR__ . '/../vendor/autoload.php';
// Bootstrap the framework
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$exists = class_exists('\\App\\Http\\Livewire\\Schedules');
echo "class_exists: "; var_export($exists); echo PHP_EOL;
if ($exists) {
    try {
        $c = new \App\Http\Livewire\Schedules();
        echo 'instantiated: ' . get_class($c) . PHP_EOL;
        try {
                    // Try mounting via Livewire manager to mimic Blade behavior
                    try {
                        $mounted = app('livewire')->mount('App\\Http\\Livewire\\Schedules');
                        echo "Mounted via manager preview: \n" . substr($mounted->html(), 0, 1000) . "\n";
                    } catch (Throwable $me) {
                        echo 'manager mount error: ' . $me->getMessage() . PHP_EOL;
                    }
        } catch (Throwable $e) {
            echo 'render error: ' . $e->getMessage() . PHP_EOL;
        }
    } catch (Throwable $e) {
        echo 'instantiation error: ' . $e->getMessage() . PHP_EOL;
    }
}

// List livewire components known to the manager
try {
    $manager = app('livewire');
    $components = $manager->getComponents();
    echo 'registered components: ' . PHP_EOL;
    foreach ($components as $name => $class) {
        echo "- $name => $class" . PHP_EOL;
    }
} catch (Throwable $e) {
    echo 'manager error: ' . $e->getMessage() . PHP_EOL;
}
