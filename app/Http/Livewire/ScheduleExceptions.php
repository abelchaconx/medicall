<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Schedule;
use App\Models\ScheduleException;
use Illuminate\Support\Str;

class ScheduleExceptions extends Component
{
    use WithPagination;

    public $selectedScheduleId;
    public $date;
    public $type = 'cancel';
    public $start_time;
    public $end_time;
    public $reason;
    public $exceptionId;
    public $perPage = 10;

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $rules = [
        'selectedScheduleId' => 'required|exists:schedules,id',
        'date' => 'required|date',
        'type' => 'required|string',
        'start_time' => 'nullable',
        'end_time' => 'nullable',
        'reason' => 'nullable|string',
    ];

    public function render()
    {
        $schedules = Schedule::with('doctorMedicalOffice.medicalOffice','doctorMedicalOffice.doctor.user')->orderBy('id','desc')->get();
        $exceptions = collect();
        if ($this->selectedScheduleId) {
            $exceptions = ScheduleException::where('schedule_id', $this->selectedScheduleId)->latest()->paginate($this->perPage);
        }

        return view('livewire.schedule-exceptions', [
            'schedules' => $schedules,
            'exceptions' => $exceptions,
        ]);
    }

    public function selectSchedule($id)
    {
        $this->selectedScheduleId = $id;
        $this->resetPage();
        $this->resetForm();
    }

    public function updatedSelectedScheduleId($value)
    {
        $this->selectSchedule($value);
    }

    public function resetForm()
    {
        $this->date = null;
    $this->type = 'cancel';
        $this->start_time = null;
        $this->end_time = null;
        $this->reason = null;
        $this->exceptionId = null;
    }

    public function edit($id)
    {
        $e = ScheduleException::findOrFail($id);
        $this->exceptionId = $e->id;
        $this->selectedScheduleId = $e->schedule_id;
        $this->date = $e->date?->toDateString();
        $this->type = $e->type;
        $this->start_time = $e->start_time;
        $this->end_time = $e->end_time;
        $this->reason = $e->reason;
    }

    public function save()
    {
        $this->validate();
        if ($this->exceptionId) {
            $e = ScheduleException::findOrFail($this->exceptionId);
            $e->update([
                'date' => $this->date,
                'type' => $this->type,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'reason' => $this->reason,
            ]);
            $this->sendToast('green', 'Excepción actualizada');
        } else {
            ScheduleException::create([
                'schedule_id' => $this->selectedScheduleId,
                'date' => $this->date,
                'type' => $this->type,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'reason' => $this->reason,
            ]);
            $this->sendToast('green', 'Excepción creada');
        }
    $this->resetForm();
    try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function delete($id)
    {
        $e = ScheduleException::findOrFail($id);
        $e->delete();
        $this->sendToast('orange', 'Excepción eliminada');
    $this->resetForm();
    try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    protected function sendToast($type, $message)
    {
        if (method_exists($this, 'dispatchBrowserEvent')) {
            try { $this->dispatchBrowserEvent('toast', ['type' => $type, 'message' => $message]); return; } catch (\Throwable $e) { }
        }
        session()->flash('toast', ['type' => $type, 'message' => $message]);
    }
}
