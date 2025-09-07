<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');
    
    Route::get('/users/trashed', function () {
        return view('users.trashed');
    })->name('users.trashed');

    Route::get('/roles', function () {
        return view('roles.index');
    })->name('roles.index');
    
    Route::get('/roles/trashed', function () {
        return view('roles.trashed');
    })->name('roles.trashed');
    
    Route::get('/permissions', function () {
        return view('permissions.index');
    })->name('permissions.index');

    Route::get('/permissions/trashed', function () {
        return view('permissions.trashed');
    })->name('permissions.trashed');
    
    Route::get('/appointments', function () {
        return view('appointments.index');
    })->name('appointments.index');

    Route::get('/appointments/trashed', function () {
        return view('appointments.trashed');
    })->name('appointments.trashed');

    Route::get('/doctors', function () {
        return view('doctors.index');
    })->name('doctors.index');

    Route::get('/doctors/trashed', function () {
        return view('doctors.trashed');
    })->name('doctors.trashed');

    Route::get('/medical-offices', function () {
        return view('medical-offices.index');
    })->name('medical-offices.index');

    // Show single medical office; supports AJAX/json for modal fetches
    Route::get('/medical-offices/{office}', function ($officeId) {
    $office = \App\Models\MedicalOffice::with('doctors.user','doctors.specialties')->find($officeId);
        if (request()->query('ajax')) {
            if (! $office) return response()->json(['error' => 'Not found'], 404);
            return response()->json([
                'id' => $office->id,
                'name' => $office->name,
                'address' => $office->address_line,
                'province' => $office->province,
                'city' => $office->city,
                'phone' => $office->phone,
                'latitude' => $office->latitude,
                'longitude' => $office->longitude,
                'doctors' => $office->doctors->map(function($d){
                    return [
                        'id' => $d->id,
                        'name' => $d->user?->name,
                        'specialties' => $d->specialties->map(function($s){
                            return [
                                'id' => $s->id,
                                'name' => $s->name,
                                'color' => $s->color,
                                'color_translucent' => $s->color_translucent,
                            ];
                        })->values(),
                    ];
                }),
            ]);
        }

        return view('medical-offices.show', ['officeId' => $officeId]);
    })->name('medical-offices.show');

    

    Route::get('/medical-offices/trashed', function () {
        return view('medical-offices.trashed');
    })->name('medical-offices.trashed');

    Route::get('/patients', function () {
        return view('patients.index');
    })->name('patients.index');

    Route::get('/patients/trashed', function () {
        return view('patients.trashed');
    })->name('patients.trashed');

    Route::get('/prescriptions', function () {
        return view('prescriptions.index');
    })->name('prescriptions.index');

    Route::get('/prescriptions/trashed', function () {
        return view('prescriptions.trashed');
    })->name('prescriptions.trashed');

    Route::get('/payments', function () {
        return view('payments.index');
    })->name('payments.index');

    Route::get('/schedules', function () {
        return view('schedules.index');
    })->name('schedules.index');

    Route::get('/schedules/trashed', function () {
        return view('schedules.trashed');
    })->name('schedules.trashed');

    // API endpoints removed: use server-rendered data in views instead of AJAX endpoints

    // AJAX endpoint: get exceptions for a schedule
    Route::get('/schedules/{schedule}/exceptions', function ($scheduleId) {
        $s = \App\Models\Schedule::with('exceptions')->find($scheduleId);
        if (request()->query('ajax')) {
            if (! $s) return response()->json(['error' => 'Not found'], 404);
            return response()->json([
                'id' => $s->id,
                'exceptions' => $s->exceptions->map(function($e){
                    return [
                        'id' => $e->id,
                        'date' => $e->date->toDateString(),
                        'type' => $e->type,
                        'start_time' => $e->start_time,
                        'end_time' => $e->end_time,
                        'reason' => $e->reason,
                    ];
                })->values(),
            ]);
        }
        abort(404);
    })->name('schedules.exceptions');

    Route::get('/specialties', function () {
        return view('specialties.index');
    })->name('specialties.index');
    
    Route::get('/specialties/trashed', function () {
        return view('specialties.trashed');
    })->name('specialties.trashed');

    Route::get('/audits', function () {
        return view('audits.index');
    })->name('audits.index');
    
    // Ruta temporal para test
    Route::get('/test-dropdown', function () {
        return view('test-dropdown');
    })->name('test.dropdown');

    // AJAX endpoint: search doctor medical offices (used as fallback when Livewire isn't available)
    Route::get('/doctor-places/search', function () {
        $q = request()->query('q');
        $items = [];
        if ($q && strlen(trim($q)) >= 3) {
            $term = '%' . trim($q) . '%';
            $query = \App\Models\DoctorMedicaloffice::with(['doctor.user','medicalOffice'])
                ->whereHas('doctor.user', function($qq) use ($term) { $qq->where('name','like',$term); })
                ->orWhereHas('medicalOffice', function($qq) use ($term) { $qq->where('name','like',$term); });
            $results = $query->limit(20)->get();
            $items = $results->map(function($dp){
                $doctorName = data_get($dp, 'doctor.user.name') ?? ('Doctor #'.$dp->doctor_id);
                $placeName = data_get($dp, 'medicalOffice.name') ?? ('MedicalOffice #'.($dp->medical_office_id ?? ''));
                return ['id' => $dp->id, 'label' => $doctorName.' - '.$placeName];
            })->values();
        }
        return response()->json($items);
    })->name('doctor-places.search');
});

// Public API routes for testing (should be inside auth middleware in production)
    // Removed API-like endpoints for doctor-places and patients: application now uses server-rendered select options

// Public AJAX search for doctor places (no auth)
Route::get('/ajax/doctor-places/search', function () {
    $q = request()->query('q');
    $items = [];
    if ($q && strlen(trim($q)) >= 3) {
        $term = '%' . trim($q) . '%';
        $query = \App\Models\DoctorMedicaloffice::with(['doctor.user','medicalOffice'])
            ->whereHas('doctor.user', function($qq) use ($term) { $qq->where('name','like',$term); })
            ->orWhereHas('medicalOffice', function($qq) use ($term) { $qq->where('name','like',$term); });
        $results = $query->limit(20)->get();
        $items = $results->map(function($dp){
            $doctorName = data_get($dp, 'doctor.user.name') ?? ('Doctor #'.$dp->doctor_id);
            $placeName = data_get($dp, 'medicalOffice.name') ?? ('MedicalOffice #'.($dp->medical_office_id ?? ''));
            return ['id' => $dp->id, 'label' => $doctorName.' - '.$placeName];
        })->values();
    }
    return response()->json($items);
});
