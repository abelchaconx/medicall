<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DoctorMedicaloffice;
use App\Models\Patient;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes for Select2 AJAX search
Route::get('/doctor-places/search', function (Request $request) {
    $query = $request->get('q', '');
    
    $doctorPlaces = DoctorMedicaloffice::with(['doctor.user', 'medicalOffice'])
        ->when($query, function ($q) use ($query) {
            $q->whereHas('doctor.user', function ($doctorQuery) use ($query) {
                $doctorQuery->where('name', 'like', "%{$query}%");
            })->orWhereHas('medicalOffice', function ($placeQuery) use ($query) {
                $placeQuery->where('name', 'like', "%{$query}%");
            });
        })
        ->take(10)
        ->get();

    $results = $doctorPlaces->map(function ($doctorPlace) {
        $doctorName = data_get($doctorPlace, 'doctor.user.name') ?? 'Doctor #' . $doctorPlace->doctor_id;
        $placeName = data_get($doctorPlace, 'medicalOffice.name') ?? 'Medical Office #' . $doctorPlace->medical_office_id;
        
        return [
            'id' => $doctorPlace->id,
            'text' => $doctorName . ' - ' . $placeName
        ];
    });

    return response()->json([
        'results' => $results
    ]);
});

Route::get('/patients/search', function (Request $request) {
    $query = $request->get('q', '');
    
    $patients = Patient::when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('cedula', 'like', "%{$query}%");
        })
        ->take(10)
        ->get();

    $results = $patients->map(function ($patient) {
        return [
            'id' => $patient->id,
            'text' => $patient->name . ' - ' . $patient->cedula
        ];
    });

    return response()->json([
        'results' => $results
    ]);
});
