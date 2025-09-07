<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Patient;

class TrashedPatients extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    public function render()
    {
        $query = Patient::onlyTrashed()->with('user');
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })->orWhere('phone', 'like', "%{$this->search}%");
        }

        $patients = $query->latest('deleted_at')->paginate(12);

        return view('livewire.trashed-patients', [
            'patients' => $patients,
        ]);
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

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        $patient = Patient::withTrashed()->findOrFail($id);
        if (method_exists($patient, 'restore')) {
            $patient->restore();
            $this->sendToast('green', 'Paciente restaurado');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
        try { $this->resetPage(); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        $patient = Patient::withTrashed()->findOrFail($id);
        if (method_exists($patient, 'forceDelete')) {
            $patient->forceDelete();
            $this->sendToast('red', 'Paciente eliminado permanentemente');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
        try { $this->resetPage(); } catch (\Throwable $e) {}
    }

    protected function sendToast($type, $message)
    {
        $payload = ['type' => $type, 'message' => $message];
        if (method_exists($this, 'dispatch')) { try { $this->dispatch('showToast', $payload); } catch (\Throwable $e) {} }
        if (method_exists($this, 'dispatchBrowserEvent')) { try { $this->dispatchBrowserEvent('showToast', $payload); } catch (\Throwable $e) {} }
        session()->flash('toast', $payload);
    }
}
