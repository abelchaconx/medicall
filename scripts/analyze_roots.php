<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$html = view('livewire.schedules', [
    'schedules' => \App\Models\Schedule::latest()->paginate(1),
    'availableDoctorMedicalOffices' => collect(),
    'availableDoctors' => \App\Models\Doctor::with('user')->get()->mapWithKeys(function($d){ return [$d->id => optional($d->user)->name ?: ('Doctor #' . $d->id)]; }),
    'showForm' => false,
])->render();

// crude count of top-level element nodes
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
$body = $dom->getElementsByTagName('body')->item(0);
$elementCount = 0;
foreach ($body->childNodes as $child) {
    if ($child->nodeType === XML_ELEMENT_NODE) $elementCount++;
}
echo "Top-level element nodes in rendered HTML: $elementCount\n";
foreach ($body->childNodes as $child){
    if ($child->nodeType === XML_ELEMENT_NODE) echo ' - ' . $child->nodeName . PHP_EOL;
}
echo "\n--- Top-level node HTML previews ---\n";
foreach ($body->childNodes as $child) {
    if ($child->nodeType === XML_ELEMENT_NODE) {
        $htmlSnippet = $dom->saveHTML($child);
        echo substr(trim(preg_replace('/\s+/', ' ', $htmlSnippet)), 0, 400) . "\n---\n";
    }
}
