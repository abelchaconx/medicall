<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;

class TrashedRoles extends Component
{
    use WithPagination;

    public $perPage = 10;
    protected $paginationTheme = 'tailwind';

    protected $listeners = ['confirmActionRoles' => 'handleConfirmedAction'];

    public function render()
    {
        $roles = Role::onlyTrashed()->orderBy('deleted_at','desc')->paginate($this->perPage);
        return view('livewire.trashed-roles', compact('roles'));
    }

    public function restore($id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $role->restore();
        $this->sendToast('green', 'Rol restaurado');
        $this->resetPage();
    }

    public function forceDelete($id)
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        // detach permissions to keep DB consistent
        try {
            $role->permissions()->detach();
        } catch (\Throwable $e) {}
        $role->forceDelete();
        $this->sendToast('red', 'Rol eliminado permanentemente');
        $this->resetPage();
    }

    protected function sendToast(string $type, string $message)
    {
        $payload = ['type' => $type, 'message' => $message];
        if (is_callable([$this, 'emit'])) {
            try { $this->emit('showToast', $payload); } catch (\Throwable $e) {}
        }
        if (method_exists($this, 'dispatchBrowserEvent') && is_callable([$this, 'dispatchBrowserEvent'])) {
            $this->dispatchBrowserEvent('showToast', $payload);
        }
        session()->flash('toast', $payload);
    }

    public function handleConfirmedAction($action, $id)
    {
        if ($action === 'restore') {
            $this->restore($id);
            return;
        }

        if ($action === 'forceDelete') {
            $this->forceDelete($id);
            return;
        }
    }
}
