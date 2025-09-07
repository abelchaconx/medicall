<?php

// Test script to debug the weekdays_array issue

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Schedule weekdays_array...\n";
    
    // Test schedule with ID 9 (the one that's causing the error)
    $schedule = App\Models\Schedule::where('doctor_medicaloffice_id', 9)->first();
    
    if ($schedule) {
        echo "Found schedule for doctor_medicaloffice_id 9:\n";
        echo "ID: " . $schedule->id . "\n";
        echo "weekday: " . var_export($schedule->weekday, true) . "\n";
        echo "weekdays: " . var_export($schedule->weekdays, true) . "\n";
        
        // Test the weekdays_array attribute
        $weekdaysArray = $schedule->weekdays_array;
        echo "weekdays_array type: " . gettype($weekdaysArray) . "\n";
        echo "weekdays_array value: " . var_export($weekdaysArray, true) . "\n";
        
        // Test if it's actually an array
        if (is_array($weekdaysArray)) {
            echo "✅ weekdays_array is an array\n";
            echo "Array contents: " . implode(', ', $weekdaysArray) . "\n";
        } else {
            echo "❌ weekdays_array is NOT an array - this could cause the error!\n";
        }
        
        // Test the relational weekdays
        $relWeekdays = $schedule->weekdaysRelation()->pluck('weekday')->filter()->values()->all();
        echo "Relational weekdays: " . var_export($relWeekdays, true) . "\n";
        
    } else {
        echo "No schedule found for doctor_medicaloffice_id 9\n";
        
        // Show all schedules
        $schedules = App\Models\Schedule::all();
        echo "All schedules:\n";
        foreach ($schedules as $s) {
            echo "Schedule ID: {$s->id}, doctor_medicaloffice_id: {$s->doctor_medicaloffice_id}, weekdays_array: " . var_export($s->weekdays_array, true) . "\n";
        }
    }
    
    echo "\n✅ Test completed successfully\n";
    
} catch (Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
