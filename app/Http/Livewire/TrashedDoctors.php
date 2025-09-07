<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Doctor;

class TrashedDoctors extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    public function render()
    {
        $query = Doctor::onlyTrashed();
        if ($this->search) {
            $query->where('license_number', 'like', "%{$this->search}%");
        }

        $doctors = $query->latest('deleted_at')->paginate(12);

        return view('livewire.trashed-doctors', [
            'doctors' => $doctors,
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
        $doctor = Doctor::withTrashed()->findOrFail($id);
        if (method_exists($doctor, 'restore')) {
            $doctor->restore();
            $this->dispatchBrowserEvent('toast', ['type' => 'green', 'message' => 'Doctor restaurado']);
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        $doctor = Doctor::withTrashed()->findOrFail($id);
        if (method_exists($doctor, 'forceDelete')) {
            $doctor->forceDelete();
            $this->dispatchBrowserEvent('toast', ['type' => 'red', 'message' => 'Doctor eliminado permanentemente']);
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }
}
