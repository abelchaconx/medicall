<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Schedule;

class TrashedSchedules extends Component
{
    use WithPagination;

    public $perPage = 12;
    protected $paginationTheme = 'tailwind';
    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    public function render()
    {
        $schedules = Schedule::onlyTrashed()->with('doctorMedicalOffice.doctor.user','doctorMedicalOffice.medicalOffice')->orderBy('deleted_at','desc')->paginate($this->perPage);
        return view('livewire.trashed-schedules', compact('schedules'));
    }

    public function restore($id)
    {
        $s = Schedule::onlyTrashed()->findOrFail($id);
        $s->restore();
    $this->sendToast('green', 'Horario restaurado');
        $this->resetPage();
    }

    public function forceDelete($id)
    {
        $s = Schedule::onlyTrashed()->findOrFail($id);
        $s->forceDelete();
        $this->sendToast('red', 'Horario eliminado permanentemente');
        $this->resetPage();
    }

    protected function sendToast($type, $message)
    {
        if (method_exists($this, 'dispatchBrowserEvent')) {
            try {
                $this->dispatchBrowserEvent('toast', ['type' => $type, 'message' => $message]);
                return;
            } catch (\Throwable $e) {
                // fall through to session flash
            }
        }

        session()->flash('toast', ['type' => $type, 'message' => $message]);
    }

    public function confirmAction($action, $id = null)
    {
        if (is_array($action) && isset($action['action'])) {
            $id = $action['id'] ?? $id;
            $action = $action['action'];
        }

        if ($action === 'restore') return $this->restore($id);
        if ($action === 'forceDelete') return $this->forceDelete($id);
    }
}
