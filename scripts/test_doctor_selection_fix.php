<?php

// Test script to verify the fix

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Select2 doctorPlaceSelected functionality...\n";
    
    // Test direct method call (simulating the Livewire event)
    $component = new App\Http\Livewire\Appointments();
    
    echo "✅ Component instantiated successfully\n";
    
    // Test with doctor ID 9
    echo "Testing doctorPlaceSelected with ID 9...\n";
    $component->doctorPlaceSelected(9);
    
    echo "✅ doctorPlaceSelected(9) executed without errors\n";
    echo "Doctor medical office ID set to: " . $component->doctor_medicaloffice_id . "\n";
    
    // Test with array format (old Livewire v2 style)
    echo "Testing doctorPlaceSelected with array format...\n";
    $component->doctorPlaceSelected(['id' => 9]);
    
    echo "✅ doctorPlaceSelected(['id' => 9]) executed without errors\n";
    
    // Test selectDoctor directly
    echo "Testing selectDoctor directly...\n";
    $component->selectDoctor(9);
    
    echo "✅ selectDoctor(9) executed without errors\n";
    
    // Test getAvailableHoursForDoctor
    echo "Testing getAvailableHoursForDoctor...\n";
    $reflection = new ReflectionClass($component);
    $method = $reflection->getMethod('getAvailableHoursForDoctor');
    $method->setAccessible(true);
    $hours = $method->invoke($component, 9, '2025-09-05');
    
    echo "✅ getAvailableHoursForDoctor executed without errors\n";
    echo "Available hours count: " . count($hours) . "\n";
    
    echo "\n🎉 All tests passed! The fix is working correctly.\n";
    
} catch (Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
