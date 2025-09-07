<?php
namespace App\Http\Livewire;

use Livewire\WithPagination;
use App\Models\medicaloffice;
use App\Http\Livewire\MedicalOffices;

class TrashedMedicaloffice extends MedicalOffices
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $medicalofficeId;
    public $name;
    public $address;

    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
    ];

    public function render()
    {
        $query = medicaloffice::query();
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('address', 'like', "%{$this->search}%");
        }

        $medicaloffices = $query->latest()->paginate(12);

        return view('livewire.medicaloffices', [
            'medicaloffices' => $medicaloffices,
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
        $medicaloffice = medicaloffice::withTrashed()->findOrFail($id);
        $this->medicalofficeId = $medicaloffice->id;
        $this->name = $medicaloffice->name;
        $this->address = $medicaloffice->address ?? ($medicaloffice->address_line ?? null);
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->medicalofficeId) {
            $medicaloffice = medicaloffice::withTrashed()->findOrFail($this->medicalofficeId);
            $medicaloffice->update([
                'name' => $this->name,
                'address' => $this->address,
            ]);
            $this->sendToast('green', 'Lugar actualizado');
        } else {
            medicaloffice::create([
                'name' => $this->name,
                'address' => $this->address,
            ]);
            $this->sendToast('green', 'Lugar creado');
        }

    $this->resetForm();
    $this->showForm = false;
    try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->medicalofficeId = null;
        $this->name = null;
        $this->address = null;
        $this->showForm = false;
    }

    public function performSearch()
    {
        // trigger a re-render — search is bound
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
        $medicaloffice = medicaloffice::findOrFail($id);
        if (method_exists($medicaloffice, 'delete')) {
            $medicaloffice->delete();
            $this->sendToast('orange', 'Lugar eliminado');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(medicaloffice::class, 'withTrashed')) {
            $medicaloffice = medicaloffice::withTrashed()->findOrFail($id);
            if (method_exists($medicaloffice, 'restore')) {
                $medicaloffice->restore();
                $this->sendToast('green', 'Lugar restaurado');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(medicaloffice::class, 'withTrashed')) {
            $medicaloffice = medicaloffice::withTrashed()->findOrFail($id);
            if (method_exists($medicaloffice, 'forceDelete')) {
                $medicaloffice->forceDelete();
                $this->sendToast('red', 'Lugar eliminado permanentemente');
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
