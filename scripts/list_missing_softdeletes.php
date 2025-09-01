<?php
$files = glob(__DIR__ . '/../database/migrations/*.php');
$missing = [];
foreach ($files as $f) {
    $c = file_get_contents($f);
    if (strpos($c, 'softDeletes') === false) {
        $missing[] = basename($f);
    }
}
echo "Migrations missing softDeletes:\n";
foreach ($missing as $m) echo " - $m\n";
