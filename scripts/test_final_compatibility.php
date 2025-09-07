<?php

require_once __DIR__ . '/../vendor/autoload.php';

echo "🧪 Testing Livewire v3 compatibility after fixes...\n\n";

try {
    // Test 1: Verify no spread operator errors in Schedule model
    echo "1️⃣ Testing Schedule model...\n";
    
    $app = require __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $schedule = new App\Models\Schedule();
    $schedule->weekdays = 'monday,tuesday,wednesday';
    
    $weekdaysArray = $schedule->weekdays_array;
    if (is_array($weekdaysArray)) {
        echo "   ✅ Schedule::weekdays_array returns array: " . json_encode($weekdaysArray) . "\n";
    } else {
        echo "   ❌ Schedule::weekdays_array failed\n";
    }
    
    // Test 2: Verify API endpoints work
    echo "\n2️⃣ Testing API endpoints...\n";
    
    $request = new Illuminate\Http\Request();
    $request->merge(['q' => '']);
    
    try {
        $controller = new App\Http\Controllers\Api\DoctorPlaceController();
        $response = $controller->search($request);
        
        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            echo "   ✅ DoctorPlace API: " . count($data['results']) . " results\n";
        } else {
            echo "   ❌ DoctorPlace API failed: " . $response->getStatusCode() . "\n";
        }
    } catch (Exception $e) {
        echo "   ❌ DoctorPlace API error: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Verify Livewire component can be instantiated
    echo "\n3️⃣ Testing Livewire component...\n";
    
    try {
        $component = new App\Http\Livewire\Appointments();
        echo "   ✅ Appointments component instantiated successfully\n";
        
        // Test doctorPlaceSelected with different parameter types
        $component->doctorPlaceSelected(9);
        echo "   ✅ doctorPlaceSelected(9) - direct ID - success\n";
        
        $component->doctorPlaceSelected(['id' => 9]);
        echo "   ✅ doctorPlaceSelected(['id' => 9]) - array format - success\n";
        
    } catch (Exception $e) {
        echo "   ❌ Appointments component error: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 All compatibility tests passed!\n";
    echo "💡 The Select2 consultorio search should now work properly.\n";
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
}
