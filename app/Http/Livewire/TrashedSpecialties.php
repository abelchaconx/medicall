<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Specialty;

class TrashedSpecialties extends Component
{
    use WithPagination;

    public $perPage = 10;
    protected $paginationTheme = 'tailwind';

    protected $listeners = ['confirmActionSpecialties' => 'handleConfirmedAction'];

    public function render()
    {
        $specialties = Specialty::onlyTrashed()->orderBy('deleted_at','desc')->paginate($this->perPage);
        return view('livewire.trashed-specialties', compact('specialties'));
    }

    public function restore($id)
    {
        $s = Specialty::onlyTrashed()->findOrFail($id);
        $s->restore();
        $this->sendToast('green','Especialidad restaurada');
        $this->resetPage();
    }

    public function forceDelete($id)
    {
        $s = Specialty::onlyTrashed()->findOrFail($id);
        $s->forceDelete();
        $this->sendToast('red','Especialidad eliminada permanentemente');
        $this->resetPage();
    }

    public function handleConfirmedAction($action, $id)
    {
        if ($action === 'restore') return $this->restore($id);
        if ($action === 'forceDelete') return $this->forceDelete($id);
    }

    protected function sendToast($type, $message)
    {
        $payload = ['type' => $type, 'message' => $message];
    if (method_exists($this, 'dispatch')) { try { $this->dispatch('showToast', $payload); } catch (\Throwable $e) {} }
        if (method_exists($this, 'dispatchBrowserEvent')) { $this->dispatchBrowserEvent('showToast', $payload); }
        session()->flash('toast', $payload);
    }
}
