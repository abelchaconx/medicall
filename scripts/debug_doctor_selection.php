<?php

require_once __DIR__ . '/../vendor/autoload.php';

echo "🧪 Testing specific doctor selection (ID: 9)...\n\n";

try {
    // Bootstrap Laravel
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    
    // Simulate the exact flow that's causing the error
    echo "1️⃣ Creating Appointments component...\n";
    $component = new App\Http\Livewire\Appointments();
    
    echo "2️⃣ Testing doctorPlaceSelected with ID 9...\n";
    try {
        $component->doctorPlaceSelected(9);
        echo "   ✅ doctorPlaceSelected(9) executed successfully\n";
    } catch (Exception $e) {
        echo "   ❌ Error in doctorPlaceSelected: " . $e->getMessage() . "\n";
        echo "   📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
        echo "   📋 Stack trace:\n";
        foreach ($e->getTrace() as $i => $trace) {
            $file = isset($trace['file']) ? basename($trace['file']) : 'unknown';
            $line = isset($trace['line']) ? $trace['line'] : 'unknown';
            $function = isset($trace['function']) ? $trace['function'] : 'unknown';
            echo "      #$i $file:$line $function()\n";
            if ($i >= 5) break; // Limit to first 5 stack frames
        }
    }
    
    echo "\n3️⃣ Testing selectDoctor directly...\n";
    try {
        $component->selectDoctor(9);
        echo "   ✅ selectDoctor(9) executed successfully\n";
    } catch (Exception $e) {
        echo "   ❌ Error in selectDoctor: " . $e->getMessage() . "\n";
        echo "   📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    }
    
    echo "\n4️⃣ Testing getAvailableHoursForDoctor...\n";
    try {
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('getAvailableHoursForDoctor');
        $method->setAccessible(true);
        
        $result = $method->invoke($component, 9, '2025-09-05');
        echo "   ✅ getAvailableHoursForDoctor returned: " . count($result) . " slots\n";
    } catch (Exception $e) {
        echo "   ❌ Error in getAvailableHoursForDoctor: " . $e->getMessage() . "\n";
        echo "   📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    }
    
    echo "\n5️⃣ Testing Schedule model with ID 9 relation...\n";
    try {
        $schedules = App\Models\Schedule::where('doctor_medicaloffice_id', 9)->get();
        echo "   ✅ Found " . $schedules->count() . " schedules for doctor_medicaloffice_id 9\n";
        
        foreach ($schedules as $schedule) {
            $weekdays = $schedule->weekdays_array;
            echo "   📅 Schedule ID {$schedule->id}: weekdays = " . json_encode($weekdays) . "\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error in Schedule query: " . $e->getMessage() . "\n";
        echo "   📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    }
    
    echo "\n✅ All tests completed!\n";
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    echo "📋 Stack trace:\n";
    foreach ($e->getTrace() as $i => $trace) {
        $file = isset($trace['file']) ? basename($trace['file']) : 'unknown';
        $line = isset($trace['line']) ? $trace['line'] : 'unknown';
        $function = isset($trace['function']) ? $trace['function'] : 'unknown';
        echo "   #$i $file:$line $function()\n";
        if ($i >= 10) break;
    }
}
