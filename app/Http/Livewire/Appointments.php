<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\DoctorMedicaloffice as DoctorPlace;
use App\Models\Schedule;
use Carbon\Carbon;

class Appointments extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $appointmentId;
    public $patient_id;
    public $doctor_medicaloffice_id;
    public $schedule_id;
    public $start_datetime;
    public $end_datetime;
    public $status;
    public $notes;
    public $consultation_type;
    public $consultation_notes;
    // calendar UI state
    public $calendarMonth; // Y-m-01 string
    public $selected_date; // Y-m-d
    public $available_hours = []; // array of timeslots for selected doctor/date
    public $dailyAvailability = []; // map date => percentage available
    // consultorio search UI
    public $consultorio_search = '';

    protected $listeners = [
        'confirmAction',
        'refreshComponent' => '$refresh',
        // events from JS/select2 (Livewire v3 format)
        'doctorPlaceSelected',
        'patientSelected',
    ];

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'doctor_medicaloffice_id' => 'required|exists:doctor_medicaloffices,id',
    'start_datetime' => 'required|date',
    'end_datetime' => 'nullable',
    'schedule_id' => 'nullable|exists:schedules,id',
    'consultation_type' => 'nullable|string',
    'consultation_notes' => 'nullable|string',
    'status' => 'nullable|in:pending,confirmed,cancelled,atendido',
        'notes' => 'nullable|string',
    ];

    public function render()
    {
        $query = Appointment::query();
        if ($this->search) {
            $query->where('notes', 'like', "%{$this->search}%")->orWhereHas('patient', function($q){ $q->where('name','like', "%{$this->search}%"); });
        }

        $appointments = $query->latest()->paginate(12);

        // Patients table doesn't have a 'name' column; use related User name instead
        $availablePatients = Patient::with('user')->get()->mapWithKeys(function($p){
            return [$p->id => optional($p->user)->name ?? ('Paciente #' . $p->id)];
        });
        $availableDoctorMedicalOffices = DoctorPlace::with(['doctor.user','medicalOffice'])->get()->mapWithKeys(function($dp){
            $doctorName = data_get($dp, 'doctor.user.name') ?? ('Doctor #'.$dp->doctor_id);
            $placeName = data_get($dp, 'medicalOffice.name') ?? ('MedicalOffice #'.($dp->medical_office_id ?? ''));
            return [$dp->id => $doctorName.' - '.$placeName];
        });

        // initialize calendar month on first render
        if (! $this->calendarMonth) $this->calendarMonth = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-01');

        // if selected_date and doctor set, ensure available_hours are computed
        if ($this->selected_date && $this->doctor_medicaloffice_id) {
            $this->available_hours = $this->getAvailableHoursForDoctor($this->doctor_medicaloffice_id, $this->selected_date);
        }

        // compute daily availability percentages for the calendar if doctor selected
        if ($this->doctor_medicaloffice_id && $this->calendarMonth) {
            $this->dailyAvailability = $this->computeDailyAvailability($this->doctor_medicaloffice_id, $this->calendarMonth);
        } else {
            $this->dailyAvailability = [];
        }

        return view('livewire.appointments', [
            'appointments' => $appointments,
            'availablePatients' => $availablePatients,
            'availableDoctorMedicalOffices' => $availableDoctorMedicalOffices,
            'dailyAvailability' => $this->dailyAvailability,
        ]);
    }

    /**
     * Compute availability percentage for each day in the month starting at $monthStart (Y-m-01)
     * Returns array keyed by Y-m-d => percentage (int 0..100)
     */
    protected function computeDailyAvailability($doctorMedicalOfficeId, $monthStart)
    {
        $map = [];
        try {
            $start = \Carbon\Carbon::parse($monthStart)->startOfMonth();
            $end = \Carbon\Carbon::parse($monthStart)->endOfMonth();
        } catch (\Throwable $e) {
            return $map;
        }

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $date = $d->format('Y-m-d');

            // total potential slots (considering schedules and exceptions but not existing appointments)
            $totalSlots = 0;
            $schedules = \App\Models\Schedule::where('doctor_medicaloffice_id', $doctorMedicalOfficeId)
                ->where(function($q) use ($date) { $q->whereNull('valid_from')->orWhere('valid_from','<=',$date); })
                ->where(function($q) use ($date) { $q->whereNull('valid_to')->orWhere('valid_to','>=',$date); })
                ->get();

            foreach ($schedules as $s) {
                $days = $s->weekdays_array ?? [];
                $weekdayIso = (int) $d->isoWeekday();
                $weekdayToMatch = $weekdayIso === 7 ? 0 : $weekdayIso;
                if (empty($days) || ! in_array($weekdayToMatch, $days)) continue;

                // exceptions for this schedule on this date
                $exceptions = \App\Models\ScheduleException::where('schedule_id', $s->id)->where('date', $date)->get();

                $startTime = $s->start_time; $endTime = $s->end_time;
                if (! $startTime || ! $endTime) continue;

                $duration = (int) ($s->duration_minutes ?: 30);
                $cur = \Carbon\Carbon::parse($date . ' ' . $startTime);
                $limit = \Carbon\Carbon::parse($date . ' ' . $endTime);
                while ($cur->lt($limit)) {
                    $slotStart = $cur->format('H:i:s');
                    $slotEnd = $cur->copy()->addMinutes($duration)->format('H:i:s');
                    if ($cur->copy()->addMinutes($duration)->gt($limit)) break;

                    // check exceptions to reduce totalSlots
                    $blocked = false;
                    foreach ($exceptions as $ex) {
                        if (empty($ex->start_time) && empty($ex->end_time)) { $blocked = true; break; }
                        if ($ex->start_time && $ex->end_time) {
                            if ($slotStart >= $ex->start_time && $slotStart < $ex->end_time) { $blocked = true; break; }
                        }
                    }
                    if (! $blocked) $totalSlots++;

                    $cur->addMinutes($duration);
                }
            }

            // available slots (respecting existing appointments and exceptions) reuse existing method
            $availableSlots = count($this->getAvailableHoursForDoctor($doctorMedicalOfficeId, $date));

            // booked slots = appointments count start_datetime on that date for this doctor
            $bookedSlots = \App\Models\Appointment::where('doctor_medicaloffice_id', $doctorMedicalOfficeId)
                ->whereDate('start_datetime', $date)
                ->count();

            $percent = 0;
            if ($totalSlots > 0) {
                $percent = (int) round(max(0, $availableSlots) / $totalSlots * 100);
            }

            $map[$date] = [
                'percent' => $percent,
                'total' => $totalSlots,
                'available' => $availableSlots,
                'booked' => $bookedSlots,
                'hasSchedules' => $schedules->count() > 0 && $totalSlots > 0,
            ];
        }

        return $map;
    }

    // calendar navigation
    public function prevMonth()
    {
        $m = \Carbon\Carbon::parse($this->calendarMonth);
        $this->calendarMonth = $m->subMonth()->startOfMonth()->format('Y-m-01');
    }

    public function nextMonth()
    {
        $m = \Carbon\Carbon::parse($this->calendarMonth);
        $this->calendarMonth = $m->addMonth()->startOfMonth()->format('Y-m-01');
    }

    public function selectDate($date)
    {
        // date expected as Y-m-d
        $this->selected_date = $date;
        if ($this->doctor_medicaloffice_id) {
            $this->available_hours = $this->getAvailableHoursForDoctor($this->doctor_medicaloffice_id, $this->selected_date);
        } else {
            $this->available_hours = [];
        }
        // Reset selected time when date changes
        $this->start_datetime = null;
        $this->end_datetime = null;
    }

    public function selectTimeSlot($startTime)
    {
        if (!$this->selected_date) {
            return;
        }

        // Combine date and time
        // Build a Carbon start with seconds and determine duration from schedule when possible
        // New signature will be selectTimeSlot($startTime, $scheduleId = null)
        // For backward compatibility accept if schedule id passed as second arg via Livewire
        $args = func_get_args();
        $scheduleId = $args[1] ?? null;
        if ($scheduleId) $this->schedule_id = (int) $scheduleId;

        try {
            $startCarbon = \Carbon\Carbon::parse($this->selected_date . ' ' . $startTime);
            $duration = 30; // default in minutes
            if ($this->schedule_id) {
                $s = \App\Models\Schedule::find($this->schedule_id);
                if ($s && $s->duration_minutes) $duration = (int) $s->duration_minutes;
            } elseif ($this->doctor_medicaloffice_id) {
                $s = \App\Models\Schedule::where('doctor_medicaloffice_id', $this->doctor_medicaloffice_id)
                    ->where('start_time','<=',$startTime.':00')
                    ->where('end_time','>',$startTime.':00')
                    ->first();
                if ($s && $s->duration_minutes) $duration = (int) $s->duration_minutes;
            }

            $this->start_datetime = $startCarbon->format('Y-m-d H:i:s');
            $this->end_datetime = $startCarbon->copy()->addMinutes($duration)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            \Log::error('Error calculating end time: ' . $e->getMessage());
            $this->start_datetime = $this->selected_date . ' ' . $startTime;
            $this->end_datetime = null;
        }
    }

    // setScheduleId removed: schedule id is passed directly to selectTimeSlot

    public function selectDoctor($doctorMedicalOfficeId)
    {
        $this->doctor_medicaloffice_id = $doctorMedicalOfficeId;
        
        // Only calculate available hours if we have a selected date
        if ($this->selected_date) {
            try {
                $this->available_hours = $this->getAvailableHoursForDoctor($this->doctor_medicaloffice_id, $this->selected_date);
            } catch (\Exception $e) {
                \Log::error('Error getting available hours for doctor ' . $doctorMedicalOfficeId . ' on date ' . $this->selected_date . ': ' . $e->getMessage());
                $this->available_hours = [];
            }
        } else {
            // No date selected yet, clear available hours
            $this->available_hours = [];
        }
    }

    public function doctorPlaceSelected($id = null)
    {
        try {
            \Log::info('doctorPlaceSelected called with ID: ' . ($id ?? 'null'));
            // Accept either an array of ids (from multiselect) or a single id
            $selectedId = null;
            if (is_array($id)) {
                // if array of numeric ids, pick the first non-empty value
                $first = null;
                foreach ($id as $v) { if ($v !== null && $v !== '') { $first = $v; break; } }
                $selectedId = $first ? (int) $first : null;
            } else {
                $selectedId = $id ? (int) $id : null;
            }

            if ($selectedId) {
                \Log::info('Calling selectDoctor with ID: ' . $selectedId);
                $this->doctor_medicaloffice_id = $selectedId;
                $this->selectDoctor($selectedId);
                \Log::info('Doctor place selection completed successfully');
            } else {
                \Log::warning('Doctor place selected but no valid ID provided');
                $this->doctor_medicaloffice_id = null;
                $this->available_hours = [];
            }
        } catch (\Exception $e) {
            \Log::error('Error in doctorPlaceSelected: ' . $e->getMessage() . ' - Stack: ' . $e->getTraceAsString());
            // Reset to avoid broken state
            $this->doctor_medicaloffice_id = null;
            $this->available_hours = [];
        }
    }

    public function patientSelected($id = null)
    {
        // Accept array or single id; set patient_id for the form
        $selectedId = null;
        if (is_array($id)) {
            $first = null;
            foreach ($id as $v) { if ($v !== null && $v !== '') { $first = $v; break; } }
            $selectedId = $first ? (int) $first : null;
        } else {
            $selectedId = $id ? (int) $id : null;
        }

        if ($selectedId) {
            $this->patient_id = $selectedId;
        }
    }

    /**
     * Check if a doctor has any availability on a given date
     */
    public function isDoctorAvailableOnDate($doctorMedicalOfficeId, $date): bool
    {
        if (! $doctorMedicalOfficeId || ! $date) return false;
        
        try { $dt = \Carbon\Carbon::parse($date); } catch (\Throwable $e) { return false; }
        
        $weekdayIso = (int) $dt->isoWeekday();
        $weekdayToMatch = $weekdayIso === 7 ? 0 : $weekdayIso;
        
        // check if there are any schedules for this doctor on this weekday
        $hasSchedule = \App\Models\Schedule::where('doctor_medicaloffice_id', $doctorMedicalOfficeId)
            ->where(function($q) use ($date) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $date);
            })->where(function($q) use ($date) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $date);
            })->whereHas('weekdaysRelation', function($q) use ($weekdayToMatch) {
                $q->where('weekday', $weekdayToMatch);
            })->exists();
            
        if (!$hasSchedule) {
            // fallback to legacy weekday check
            $hasSchedule = \App\Models\Schedule::where('doctor_medicaloffice_id', $doctorMedicalOfficeId)
                ->where(function($q) use ($date) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', $date);
                })->where(function($q) use ($date) {
                    $q->whereNull('valid_to')->orWhere('valid_to', '>=', $date);
                })->where('weekday', $weekdayToMatch)->exists();
        }
        
        if (!$hasSchedule) return false;
        
        // check if there are full-day exceptions
        $hasFullDayException = \App\Models\ScheduleException::whereHas('schedule', function($q) use ($doctorMedicalOfficeId) {
            $q->where('doctor_medicaloffice_id', $doctorMedicalOfficeId);
        })->where('date', $date)
          ->where(function($q) {
              $q->whereNull('start_time')->whereNull('end_time');
          })->exists();
          
        return !$hasFullDayException;
    }

    /**
     * Return available time slots (HH:MM) for a doctor_medicaloffice on a given date.
     */
    protected function getAvailableHoursForDoctor($doctorMedicalOfficeId, $date)
    {
        $result = [];
        if (! $doctorMedicalOfficeId || ! $date) return $result;

        try { $dt = \Carbon\Carbon::parse($date); } catch (\Throwable $e) { return $result; }

        $weekdayIso = (int) $dt->isoWeekday();
        $weekdayToMatch = $weekdayIso === 7 ? 0 : $weekdayIso;

        // load schedules valid that day
        $schedules = \App\Models\Schedule::where('doctor_medicaloffice_id', $doctorMedicalOfficeId)
            ->where(function($q) use ($date) { $q->whereNull('valid_from')->orWhere('valid_from','<=',$date); })
            ->where(function($q) use ($date) { $q->whereNull('valid_to')->orWhere('valid_to','>=',$date); })
            ->get();

        // gather occupied start datetimes for this doctor on that date
        $existing = \App\Models\Appointment::where('doctor_medicaloffice_id', $doctorMedicalOfficeId)
            ->whereDate('start_datetime', $date)
            ->pluck('start_datetime')->map(function($v){ return \Carbon\Carbon::parse($v)->format('H:i:s'); })->toArray();

        foreach ($schedules as $s) {
            $days = $s->weekdays_array ?? [];
            
            // Ensure $days is always an array
            if (!is_array($days)) {
                $days = [];
            }
            
            if (empty($days) || ! in_array($weekdayToMatch, $days)) continue;

            if (\App\Models\ScheduleException::where('schedule_id', $s->id)->where('date', $date)->exists()) {
                // if exception exists for that date, skip this schedule (exceptions may be partial; handled later)
                $exceptions = \App\Models\ScheduleException::where('schedule_id', $s->id)->where('date', $date)->get();
            } else {
                $exceptions = collect();
            }

            $start = $s->start_time; $end = $s->end_time;
            if (! $start || ! $end) continue;

            $duration = (int) ($s->duration_minutes ?: 30);

            $cur = \Carbon\Carbon::parse($date . ' ' . $start);
            $limit = \Carbon\Carbon::parse($date . ' ' . $end);
            while ($cur->addMinutes(0)->lt($limit)) {
                $slotStart = $cur->format('H:i:s');
                $slotEnd = $cur->copy()->addMinutes($duration)->format('H:i:s');
                if ($cur->copy()->addMinutes($duration)->gt($limit)) break;

                // skip if occupied
                if (in_array($slotStart, $existing)) { $cur->addMinutes($duration); continue; }

                // skip if exception blocks this slot
                $blocked = false;
                foreach ($exceptions as $ex) {
                    if (empty($ex->start_time) && empty($ex->end_time)) { $blocked = true; break; }
                    if ($ex->start_time && $ex->end_time) {
                        if ($slotStart >= $ex->start_time && $slotStart < $ex->end_time) { $blocked = true; break; }
                    }
                }
                if (! $blocked) {
                    $result[] = [
                        'start' => $cur->format('H:i'),
                        'end' => $cur->copy()->addMinutes($duration)->format('H:i'),
                        'schedule_id' => $s->id,
                    ];
                }

                $cur->addMinutes($duration);
            }
        }

        // unique and sort
        $result = collect($result)->unique(function($i){ return $i['start']; })->sortBy('start')->values()->all();
        return $result;
    }

    public function selectSlot($time)
    {
        // $time is like 'HH:MM'
        if (! $this->selected_date) return;
        $start = \Carbon\Carbon::parse($this->selected_date . ' ' . $time)->format('Y-m-d H:i:00');
        $duration = 30; // default
        // try to read schedule duration if available
        if ($this->doctor_medicaloffice_id) {
            $s = \App\Models\Schedule::where('doctor_medicaloffice_id', $this->doctor_medicaloffice_id)
                ->where('start_time','<=',$time.':00')->where('end_time','>',$time.':00')->first();
            if ($s && $s->duration_minutes) $duration = (int)$s->duration_minutes;
        }
        $end = \Carbon\Carbon::parse($start)->addMinutes($duration)->format('Y-m-d H:i:00');
        $this->start_datetime = $start;
        $this->end_datetime = $end;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        // NO pre-seleccionar nada para forzar flujo secuencial
        // El usuario debe seleccionar consultorio manualmente
    }

    public function edit($id)
    {
        $a = Appointment::withTrashed()->findOrFail($id);
        $this->appointmentId = $a->id;
        $this->patient_id = $a->patient_id;
        $this->doctor_medicaloffice_id = $a->doctor_medicaloffice_id;
        $this->start_datetime = $a->start_datetime;
        $this->end_datetime = $a->end_datetime;
        $this->status = $a->status;
        $this->notes = $a->notes;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        // Normalize datetimes to a standard format to avoid equality/compare issues
        try {
            if ($this->start_datetime) {
                $this->start_datetime = Carbon::parse($this->start_datetime)->format('Y-m-d H:i:s');
            }
            if ($this->end_datetime) {
                $this->end_datetime = Carbon::parse($this->end_datetime)->format('Y-m-d H:i:s');
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed normalizing datetimes in save(): ' . $e->getMessage());
        }

        // check duplicate appointment start for the same doctor_medicaloffice
        $existsQuery = Appointment::where('doctor_medicaloffice_id', $this->doctor_medicaloffice_id)
            ->where('start_datetime', $this->start_datetime);
        if ($this->appointmentId) $existsQuery->where('id', '!=', $this->appointmentId);
        if ($existsQuery->exists()) {
            $this->addError('start_datetime', 'Ya existe una cita con la misma fecha/hora para este consultorio.');
            return;
        }

        // availability: ensure there is a Schedule for this doctor_medicaloffice that covers the appointment datetime
        if (! $this->isDoctorAvailableAt($this->doctor_medicaloffice_id, $this->start_datetime)) {
            $this->addError('start_datetime', 'El doctor no tiene un horario registrado para esta fecha/hora.');
            return;
        }

        if ($this->appointmentId) {
            $a = Appointment::withTrashed()->findOrFail($this->appointmentId);
            $a->update([
                'patient_id' => $this->patient_id,
                'doctor_medicaloffice_id' => $this->doctor_medicaloffice_id,
                'start_datetime' => $this->start_datetime,
                'end_datetime' => $this->end_datetime,
                'status' => $this->status ?? 'pending',
                'notes' => $this->notes,
                'schedule_id' => $this->schedule_id,
                'consultation_type' => $this->consultation_type,
                'consultation_notes' => $this->consultation_notes,
            ]);
            $this->sendToast('green','Cita actualizada');
        } else {
            Appointment::create([
                'patient_id' => $this->patient_id,
                'doctor_medicaloffice_id' => $this->doctor_medicaloffice_id,
                'start_datetime' => $this->start_datetime,
                'end_datetime' => $this->end_datetime,
                'status' => $this->status ?? 'pending',
                'notes' => $this->notes,
                'schedule_id' => $this->schedule_id,
                'consultation_type' => $this->consultation_type,
                'consultation_notes' => $this->consultation_notes,
            ]);
            $this->sendToast('green','Cita creada');
        }

        $this->resetForm();
        $this->showForm = false;
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->appointmentId = null;
        $this->patient_id = null;
        $this->doctor_medicaloffice_id = null;
        $this->start_datetime = null;
        $this->end_datetime = null;
        $this->status = null;
        $this->notes = null;
        $this->showForm = false;
        // Reset calendar state
        $this->selected_date = null;
        $this->available_hours = [];
    }

    public function performSearch() {}

    public function clearSearch()
    {
        $this->search = '';
    }

    public function confirmAction($action, $id = null)
    {
        if (is_array($action) && isset($action['action'])) {
            $id = $action['id'] ?? $id;
            $action = $action['action'];
        }

        if ($action === 'delete') return $this->delete($id);
        if ($action === 'restore') return $this->restore($id);
        if ($action === 'forceDelete') return $this->forceDelete($id);
    }

    public function delete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        $a = Appointment::findOrFail($id);
        if (method_exists($a,'delete')) {
            $a->delete();
            $this->sendToast('orange','Cita eliminada');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Appointment::class,'withTrashed')) {
            $a = Appointment::withTrashed()->findOrFail($id);
            if (method_exists($a,'restore')) { $a->restore(); $this->sendToast('green','Cita restaurada'); }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Appointment::class,'withTrashed')) {
            $a = Appointment::withTrashed()->findOrFail($id);
            if (method_exists($a,'forceDelete')) { $a->forceDelete(); $this->sendToast('red','Cita eliminada permanentemente'); }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    protected function sendToast($type, $message)
    {
        if (method_exists($this, 'dispatch')) {
            try { $this->dispatch('toast', ['type' => $type, 'message' => $message]); return; } catch (\Throwable $e) { }
        }

        if (method_exists($this, 'dispatchBrowserEvent')) {
            $this->dispatchBrowserEvent('toast', ['type' => $type, 'message' => $message]);
            return;
        }

        session()->flash('toast', ['type' => $type, 'message' => $message]);
    }

    /**
     * Check if there is a Schedule covering the given datetime for the provided doctor_medicaloffice
     */
    protected function isDoctorAvailableAt($doctorMedicalOfficeId, $datetime): bool
    {
        if (! $doctorMedicalOfficeId || ! $datetime) return false;
        try {
            $dt = Carbon::parse($datetime);
        } catch (\Throwable $e) { return false; }

        $dateOnly = $dt->toDateString();
        $apptTime = $dt->format('H:i:s');

        // map ISO weekday (1..7) to stored values used in schedules (legacy used 0..6 where 0 is Sunday)
        $iso = (int) $dt->isoWeekday(); // 1=Mon .. 7=Sun
        $weekdayToMatch = $iso === 7 ? 0 : $iso;

        // load candidate schedules for this doctor_medicaloffice that are valid on the appointment date
        $schedules = Schedule::where('doctor_medicaloffice_id', $doctorMedicalOfficeId)
            ->where(function($q) use ($dateOnly) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $dateOnly);
            })->where(function($q) use ($dateOnly) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $dateOnly);
            })->get();

        foreach ($schedules as $s) {
            // determine weekdays for this schedule via accessor (prefers relational weekdays)
            $days = $s->weekdays_array ?? [];
            if (empty($days)) {
                // if schedule has no weekday info, skip it
                continue;
            }

            if (! in_array($weekdayToMatch, $days)) continue;

            // check exceptions for this schedule on this date
            $hasFullDayCancel = false;
            $exceptions = $s->exceptions()->where('date', $dateOnly)->get();
            foreach ($exceptions as $ex) {
                // type could indicate cancellation or reduced hours - if start_time and end_time are empty -> full day excluded
                if (empty($ex->start_time) && empty($ex->end_time)) { $hasFullDayCancel = true; break; }
                // partial exception: if appointment time falls within the exception range, consider it blocked
                if ($ex->start_time && $ex->end_time) {
                    if ($apptTime >= $ex->start_time && $apptTime < $ex->end_time) { $hasFullDayCancel = true; break; }
                }
            }
            if ($hasFullDayCancel) continue;

            // finally, check schedule start/end times
            $start = $s->start_time;
            $end = $s->end_time;
            if (! $start || ! $end) continue;
            if ($apptTime >= $start && $apptTime < $end) return true;
        }

        return false;
    }
}
