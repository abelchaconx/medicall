<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Specialty;

class Specialties extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $specialtyId;
    public $name;

    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function render()
    {
        $query = Specialty::query();
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $specialties = $query->latest()->paginate(12);

        return view('livewire.specialties', [
            'specialties' => $specialties,
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
        $specialty = Specialty::withTrashed()->findOrFail($id);
        $this->specialtyId = $specialty->id;
        $this->name = $specialty->name;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->specialtyId) {
            $specialty = Specialty::withTrashed()->findOrFail($this->specialtyId);
            $specialty->update(['name' => $this->name]);
            $this->sendToast('green', 'Especialidad actualizada');
        } else {
            Specialty::create(['name' => $this->name]);
            $this->sendToast('green', 'Especialidad creada');
        }

    $this->resetForm();
    $this->showForm = false;
    try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->specialtyId = null;
        $this->name = null;
        $this->showForm = false;
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
        $specialty = Specialty::findOrFail($id);
        if (method_exists($specialty, 'delete')) {
            $specialty->delete();
            $this->sendToast('orange', 'Especialidad eliminada');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Specialty::class, 'withTrashed')) {
            $specialty = Specialty::withTrashed()->findOrFail($id);
            if (method_exists($specialty, 'restore')) {
                $specialty->restore();
                $this->sendToast('green', 'Especialidad restaurada');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Specialty::class, 'withTrashed')) {
            $specialty = Specialty::withTrashed()->findOrFail($id);
            if (method_exists($specialty, 'forceDelete')) {
                $specialty->forceDelete();
                $this->sendToast('red', 'Especialidad eliminada permanentemente');
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
