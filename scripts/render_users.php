<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new App\Http\Livewire\Users();
// When rendering the component outside the Livewire lifecycle (for scripts),
// ensure properties expected by the blade are set to safe defaults.
$comp->showForm = false;
$view = $comp->render();
$html = method_exists($view, 'render') ? $view->render() : (string) $view;

file_put_contents(__DIR__ . '/render_users_output.html', $html);
echo "WROTE: scripts/render_users_output.html\n";
echo "contains wire:model=search? ";
echo (strpos($html, 'wire:model="search"') !== false ? 'YES' : 'NO') . PHP_EOL;
echo "contains wire:loading? ";
echo (strpos($html, 'wire:loading') !== false ? 'YES' : 'NO') . PHP_EOL;
