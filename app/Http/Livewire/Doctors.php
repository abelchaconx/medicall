<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Str;

class Doctors extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $doctorId;
    public $user_id;
    public $license_number;
    public $bio;

    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    protected $rules = [
        'user_id' => 'nullable|exists:users,id',
        'license_number' => 'required|string|max:255',
        'bio' => 'nullable|string',
    ];

    public function render()
    {
        $query = Doctor::query();
        if ($this->search) {
            $query->where('license_number', 'like', "%{$this->search}%")
                  ->orWhere('bio', 'like', "%{$this->search}%");
        }

        $doctors = $query->latest()->paginate(12);

        $availableUsers = User::orderBy('name')->pluck('name', 'id');

        return view('livewire.doctors', [
            'doctors' => $doctors,
            'availableUsers' => $availableUsers,
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
        $doctor = Doctor::findOrFail($id);
        $this->doctorId = $doctor->id;
        $this->user_id = $doctor->user_id;
        $this->license_number = $doctor->license_number;
        $this->bio = $doctor->bio;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->doctorId) {
            $doctor = Doctor::findOrFail($this->doctorId);
            $doctor->update([
                'user_id' => $this->user_id,
                'license_number' => $this->license_number,
                'bio' => $this->bio,
            ]);
            $this->sendToast('green', 'Doctor actualizado');
        } else {
            $doctor = Doctor::create([
                'user_id' => $this->user_id,
                'license_number' => $this->license_number,
                'bio' => $this->bio,
            ]);
            $this->sendToast('green', 'Doctor creado');
        }

    $this->resetForm();
    $this->showForm = false;
    try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->doctorId = null;
        $this->user_id = null;
        $this->license_number = null;
        $this->bio = null;
        $this->showForm = false;
    }

    public function confirmAction($action, $id = null)
    {
        // normalize payloads that may arrive as arrays or objects
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
        $doctor = Doctor::findOrFail($id);
        if (method_exists($doctor, 'delete')) {
            $doctor->delete();
            $this->sendToast('orange', 'Doctor eliminado');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Doctor::class, 'withTrashed')) {
            $doctor = Doctor::withTrashed()->findOrFail($id);
            if (method_exists($doctor, 'restore')) {
                $doctor->restore();
                $this->sendToast('green', 'Doctor restaurado');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Doctor::class, 'withTrashed')) {
            $doctor = Doctor::withTrashed()->findOrFail($id);
            if (method_exists($doctor, 'forceDelete')) {
                $doctor->forceDelete();
                $this->sendToast('red', 'Doctor eliminado permanentemente');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    protected function sendToast($type, $message)
    {
        // Try Livewire v3 dispatch, then v2 emit/dispatchBrowserEvent, then session flash
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
