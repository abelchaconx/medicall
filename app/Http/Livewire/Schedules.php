<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Schedule;
use App\Models\DoctorMedicaloffice as DoctorPlace;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use Illuminate\Support\Str;

class Schedules extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $scheduleId;
    public $doctor_medicaloffice_id;
    public $doctor_id;
    public $description;
    public $showDeleted = false;
    public $weekday;
    public $weekdays;
    public $start_time;
    public $end_time;
    public $duration_minutes;
    

    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    protected $rules = [
        'doctor_medicaloffice_id' => 'required|exists:doctor_medicaloffices,id',
        'doctor_id' => 'required|exists:doctors,id',
        'description' => 'required|string|max:255',
    // weekdays may be a CSV list like "1,2,3"
    'weekday' => ['nullable', 'regex:/^[1-6](?:\s*,\s*[1-6])*$/'],
    'weekdays' => ['nullable', 'regex:/^[1-6](?:\s*,\s*[1-6])*$/'],
        'start_time' => 'nullable',
        'end_time' => 'nullable',
        'duration_minutes' => 'nullable|integer',
    ];

    // When the selected doctor changes, reset the selected consultorio
    public function updatedDoctorId($value)
    {
        $this->doctor_medicaloffice_id = null;
    }

    public function render()
    {
        // When requested, show only soft-deleted schedules
        if ($this->showDeleted) {
            $query = Schedule::onlyTrashed();
        } else {
            $query = Schedule::query();
        }
        if ($this->search) {
            $query->where('description', 'like', "%{$this->search}%");
        }

        // If a doctor is selected, filter schedules to those belonging to that doctor
        if ($this->doctor_id) {
            $query->whereHas('doctorMedicalOffice', function ($q) {
                $q->where('doctor_id', $this->doctor_id);
            });
        }

        $schedules = $query->latest()->paginate(12);

        $availableDoctors = \App\Models\Doctor::with('user')->get()->mapWithKeys(function($d){
            return [$d->id => optional($d->user)->name ? optional($d->user)->name : ('Doctor #' . $d->id)];
        });

        // If a doctor is selected, show only their places; otherwise show none (user must choose doctor first)
        $availableDoctorMedicalOffices = collect();
        if ($this->doctor_id) {
            $availableDoctorMedicalOffices = DoctorPlace::with('medicalOffice')->where('doctor_id', $this->doctor_id)->get()->mapWithKeys(function($dp) {
                $placeName = data_get($dp, 'medicalOffice.name') ?? ('MedicalOffice #' . ($dp->medical_office_id ?? ''));
                return [$dp->id => $placeName];
            });
        }

        return view('livewire.schedules', [
            'schedules' => $schedules,
            'availableDoctorMedicalOffices' => $availableDoctorMedicalOffices,
            'availableDoctors' => $availableDoctors,
        ]);
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = ! $this->showDeleted;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function performSearch() {}

    public function clearSearch()
    {
        $this->search = '';
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $schedule = Schedule::withTrashed()->findOrFail($id);
        $this->scheduleId = $schedule->id;
    $this->description = $schedule->description ?? null;
    $this->doctor_medicaloffice_id = $schedule->doctor_medicaloffice_id ?? null;
    // set doctor_id from the related doctor_medicaloffice if available
    $this->doctor_id = optional($schedule->doctorMedicalOffice)->doctor_id ?? null;
    // populate both legacy single weekday and new weekdays CSV
    $this->weekday = $schedule->weekday ?? null;
    $this->weekdays = $schedule->weekdays ?? ($schedule->weekday ? (string)$schedule->weekday : null);
        $this->start_time = $schedule->start_time ?? null;
        $this->end_time = $schedule->end_time ?? null;
        $this->duration_minutes = $schedule->duration_minutes ?? null;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        // Ensure the selected consultorio belongs to the selected doctor
        if ($this->doctor_medicaloffice_id && $this->doctor_id) {
            $belongs = DoctorPlace::where('id', $this->doctor_medicaloffice_id)
                ->where('doctor_id', $this->doctor_id)
                ->exists();
            if (! $belongs) {
                $this->addError('doctor_medicaloffice_id', 'El consultorio seleccionado no pertenece al doctor elegido.');
                return;
            }
        }

        // determine recurrence days: prefer `weekdays` input, fallback to single `weekday` for backward compat
        $inputDays = $this->parseWeekdays($this->weekdays ?: $this->weekday);

        if ($this->scheduleId) {
            $s = Schedule::withTrashed()->findOrFail($this->scheduleId);
            // when editing a single schedule, if the user provided a CSV list pick the first value
            $updWeekdays = $inputDays;

            // overlap check for update
            if ($this->checkOverlap($this->doctor_medicaloffice_id, $updWeekdays, $this->start_time, $this->end_time, $s->id)) {
                $this->addError('start_time', 'El horario se solapa con otro existente para el mismo doctor y consultorio.');
                return;
            }

            $s->update([
                'doctor_medicaloffice_id' => $this->doctor_medicaloffice_id,
                'description' => $this->description,
                'weekday' => $updWeekdays ? ($updWeekdays[0] ?? null) : null,
                'weekdays' => $updWeekdays ? implode(',', $updWeekdays) : null,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'duration_minutes' => $this->duration_minutes,
            ]);
            $this->sendToast('green', 'Horario actualizado');
        } else {
            // parse weekdays (may be CSV like "1,2,3")
            $days = $this->parseWeekdays($this->weekdays ?: $this->weekday);

            // If start/end/duration provided, generate consecutive slots and create entries for each requested day
            if ($this->start_time && $this->end_time && $this->duration_minutes) {
                $slots = $this->generateSlots($this->start_time, $this->end_time, (int) $this->duration_minutes);
                $batchId = Str::uuid()->toString();
                DB::transaction(function() use ($slots, $batchId, $days) {
                    // if no days specified, create using null weekday once
                    $targetDays = $days ?: [null];
                    foreach ($targetDays as $day) {
                        // overlap check per day
                        $dayArray = $day === null ? [] : [$day];
                        if ($this->checkOverlap($this->doctor_medicaloffice_id, $dayArray, $slots[0]['start'], end($slots)['end'])) {
                            throw new \Exception('Overlap detected for day ' . ($day ?? 'none'));
                        }
                        foreach ($slots as $slot) {
                            Schedule::create([
                                'doctor_medicaloffice_id' => $this->doctor_medicaloffice_id,
                                'description' => $this->description,
                                'weekday' => $day,
                                'weekdays' => $day ? (string)$day : null,
                                'start_time' => $slot['start'],
                                'end_time' => $slot['end'],
                                'duration_minutes' => $this->duration_minutes,
                                'batch_id' => $batchId,
                            ]);
                        }
                    }
                });
                $createdCount = count($slots) * max(1, count($days));
                $this->sendToast('green', 'Horarios creados (' . $createdCount . ')');
            } else {
                // no slots generation; create one schedule per requested day (or single if none)
                if ($days && count($days) > 0) {
                    DB::transaction(function() use ($days) {
                        foreach ($days as $day) {
                            // overlap check per day (single slot)
                            if ($this->checkOverlap($this->doctor_medicaloffice_id, [$day], $this->start_time, $this->end_time)) {
                                throw new \Exception('Overlap detected for day ' . $day);
                            }
                            Schedule::create([
                                'doctor_medicaloffice_id' => $this->doctor_medicaloffice_id,
                                'description' => $this->description,
                                'weekday' => $day,
                                'weekdays' => (string)$day,
                                'start_time' => $this->start_time,
                                'end_time' => $this->end_time,
                                'duration_minutes' => $this->duration_minutes,
                            ]);
                        }
                    });
                    $this->sendToast('green', 'Horarios creados (' . count($days) . ')');
                } else {
                    // no weekdays provided — treat as single schedule with null weekday
                    if ($this->checkOverlap($this->doctor_medicaloffice_id, [], $this->start_time, $this->end_time)) {
                        $this->addError('start_time', 'El horario se solapa con otro existente para el mismo doctor y consultorio.');
                        return;
                    }
                    Schedule::create([
                        'doctor_medicaloffice_id' => $this->doctor_medicaloffice_id,
                        'description' => $this->description,
                        'weekday' => $this->weekday,
                        'weekdays' => $this->weekday ? (string)$this->weekday : null,
                        'start_time' => $this->start_time,
                        'end_time' => $this->end_time,
                        'duration_minutes' => $this->duration_minutes,
                    ]);
                    $this->sendToast('green', 'Horario creado');
                }
            }
        }

        $this->resetForm();
        $this->showForm = false;
    try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->scheduleId = null;
    $this->doctor_medicaloffice_id = null;
    $this->doctor_id = null;
        $this->description = null;
        $this->weekday = null;
        $this->start_time = null;
        $this->end_time = null;
        $this->duration_minutes = null;
        $this->showForm = false;
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
        if ($action === 'deleteBatch') return $this->deleteBatch($id);
    }

    public function delete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        $s = Schedule::findOrFail($id);
        if (method_exists($s, 'delete')) {
            $s->delete();
            $this->sendToast('orange', 'Horario eliminado');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Schedule::class, 'withTrashed')) {
            $s = Schedule::withTrashed()->findOrFail($id);
            if (method_exists($s, 'restore')) {
                $s->restore();
                $this->sendToast('green', 'Horario restaurado');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Schedule::class, 'withTrashed')) {
            $s = Schedule::withTrashed()->findOrFail($id);
            if (method_exists($s, 'forceDelete')) {
                $s->forceDelete();
                $this->sendToast('red', 'Horario eliminado permanentemente');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    // Delete a single schedule (soft delete)
    public function deleteSingle($id)
    {
        $s = Schedule::findOrFail($id);
        $s->delete();
        $this->sendToast('orange', 'Horario eliminado');
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    // Delete all schedules belonging to a given batch_id
    public function deleteBatch($batchId)
    {
        if (! $batchId) return;
        DB::transaction(function() use ($batchId) {
            Schedule::where('batch_id', $batchId)->delete();
        });
        $this->sendToast('orange', 'Lote de horarios eliminado');
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
     * Generate consecutive time slots between start and end with given duration in minutes.
     * Last partial slot is allowed (Option B).
     * Returns array of ['start' => 'HH:MM:SS', 'end' => 'HH:MM:SS']
     */
    protected function generateSlots(string $startTime, string $endTime, int $durationMinutes): array
    {
        $slots = [];
        $start = new DateTime($startTime);
        $end = new DateTime($endTime);

        if ($start >= $end || $durationMinutes <= 0) return $slots;

        while ($start < $end) {
            $slotEnd = (clone $start)->add(new DateInterval('PT' . $durationMinutes . 'M'));
            if ($slotEnd > $end) {
                // last partial slot ends at real end
                $slots[] = ['start' => $start->format('H:i:s'), 'end' => $end->format('H:i:s')];
                break;
            }
            $slots[] = ['start' => $start->format('H:i:s'), 'end' => $slotEnd->format('H:i:s')];
            $start = $slotEnd;
        }

        return $slots;
    }

    /**
     * Parse weekday input which may be a CSV like "1,2,3" or a single digit.
     * Returns array of integers in range 1..6 or empty array.
     */
    protected function parseWeekdays($input): array
    {
        if (! $input) return [];
        // allow either single int or csv
        $parts = preg_split('/\s*,\s*/', trim((string) $input));
        $days = [];
        foreach ($parts as $p) {
            if ($p === '') continue;
            if (! is_numeric($p)) continue;
            $n = (int) $p;
            if ($n >= 1 && $n <= 6) $days[] = $n;
        }
        // de-duplicate and keep order
        return array_values(array_unique($days));
    }

    /**
     * Check if a proposed time range overlaps existing schedules for the same doctor_medicaloffice
     * weekdaysArray: array of integers 1..6; if empty array treat as null-weekday (no weekday)
     */
    protected function checkOverlap($doctorMedicalOfficeId, array $weekdaysArray, $startTime, $endTime, $excludeId = null): bool
    {
        if (! $doctorMedicalOfficeId || ! $startTime || ! $endTime) return false;

        $query = Schedule::where('doctor_medicaloffice_id', $doctorMedicalOfficeId)->whereNull('deleted_at');
        if ($excludeId) $query->where('id', '!=', $excludeId);

        // If weekdaysArray provided, match any schedule that has intersection on weekdays
        if (count($weekdaysArray) > 0) {
            // match schedules that have any of the weekdays in the relational table OR legacy `weekday` column
            $query->where(function($q) use ($weekdaysArray) {
                $q->whereHas('weekdaysRelation', function($q2) use ($weekdaysArray) {
                    $q2->whereIn('weekday', $weekdaysArray);
                })->orWhereIn('weekday', $weekdaysArray);
            });
        } else {
            // match schedules with no weekday information (legacy null) or empty CSV weekdays
            $query->where(function($q){
                $q->whereNull('weekdays')->orWhere('weekdays','')->orWhereNull('weekday');
            });
        }

        // overlapping time logic: startA < endB AND startB < endA
        $query->whereRaw("TIME(?) < end_time AND TIME(start_time) < TIME(?)", [$endTime, $startTime]);

        return $query->exists();
    }
}
