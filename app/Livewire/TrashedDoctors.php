<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Doctor;

class TrashedDoctors extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 12;
    protected $listeners = ['confirmAction' => 'handleConfirmedAction', 'refreshDoctors' => '$refresh'];

    public function render()
    {
        $query = Doctor::onlyTrashed();
        if ($this->search) {
            $query->where('license_number', 'like', "%{$this->search}%");
        }

        $doctors = $query->latest('deleted_at')->paginate($this->perPage);

        return view('livewire.trashed-doctors', [
            'doctors' => $doctors,
        ]);
    }

    public function handleConfirmedAction($action, $id = null)
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
            $this->sendToast('green', 'Doctor restaurado');
        }
        $this->resetPage();
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        $doctor = Doctor::withTrashed()->findOrFail($id);
        if (method_exists($doctor, 'forceDelete')) {
            $doctor->forceDelete();
            $this->sendToast('red', 'Doctor eliminado permanentemente');
        }
        $this->resetPage();
    }

    protected function sendToast(string $type, string $message)
    {
        $payload = ['type' => $type, 'message' => $message];
        try { \Log::info('TrashedDoctors::sendToast', $payload); } catch (\Throwable $e) {}

        if (method_exists($this, 'dispatch') && is_callable([$this, 'dispatch'])) {
            $this->dispatch('toast', $payload);
            $this->dispatch('showToast', $payload);
            return;
        }

        if (method_exists($this, 'dispatchBrowserEvent') && is_callable([$this, 'dispatchBrowserEvent'])) {
            $this->dispatchBrowserEvent('toast', $payload);
            $this->dispatchBrowserEvent('showToast', $payload);
            return;
        }

        session()->flash('toast', $payload);
    }
}
