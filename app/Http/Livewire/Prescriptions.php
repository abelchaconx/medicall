<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Prescription;

class Prescriptions extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $prescriptionId;
    public $patient_id;
    public $notes;

    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'notes' => 'nullable|string',
    ];

    public function render()
    {
        $query = Prescription::query();
        if ($this->search) {
            $query->whereHas('patient', function($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })->orWhere('notes', 'like', "%{$this->search}%");
        }

        $prescriptions = $query->latest()->paginate(12);

        return view('livewire.prescriptions', [
            'prescriptions' => $prescriptions,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $prescription = Prescription::withTrashed()->findOrFail($id);
        $this->prescriptionId = $prescription->id;
        $this->patient_id = $prescription->patient_id;
        $this->notes = $prescription->notes;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->prescriptionId) {
            $prescription = Prescription::withTrashed()->findOrFail($this->prescriptionId);
            $prescription->update([
                'patient_id' => $this->patient_id,
                'notes' => $this->notes,
            ]);
            $this->sendToast('green', 'Prescripción actualizada');
        } else {
            Prescription::create([
                'patient_id' => $this->patient_id,
                'notes' => $this->notes,
            ]);
            $this->sendToast('green', 'Prescripción creada');
        }

    $this->resetForm();
    $this->showForm = false;
    try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->prescriptionId = null;
        $this->patient_id = null;
        $this->notes = null;
        $this->showForm = false;
    }

    public function performSearch()
    {
        // search bound to property
    }

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
        $prescription = Prescription::findOrFail($id);
        if (method_exists($prescription, 'delete')) {
            $prescription->delete();
            $this->sendToast('orange', 'Prescripción eliminada');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Prescription::class, 'withTrashed')) {
            $prescription = Prescription::withTrashed()->findOrFail($id);
            if (method_exists($prescription, 'restore')) {
                $prescription->restore();
                $this->sendToast('green', 'Prescripción restaurada');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Prescription::class, 'withTrashed')) {
            $prescription = Prescription::withTrashed()->findOrFail($id);
            if (method_exists($prescription, 'forceDelete')) {
                $prescription->forceDelete();
                $this->sendToast('red', 'Prescripción eliminada permanentemente');
            }
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
}
