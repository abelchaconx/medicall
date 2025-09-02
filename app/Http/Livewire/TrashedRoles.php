<?php

namespace App\Http\Livewire;

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
        $payload = ['type' => 'green', 'message' => 'Rol restaurado'];

        if (method_exists($this, 'dispatch') && is_callable([$this, 'dispatch'])) {
            $this->dispatch('toast', $payload);
            $this->dispatch('toast', $payload['type'] ?? '', $payload['message'] ?? '');
            $this->dispatch('showToast', $payload);
            $this->dispatch('showToast', $payload['type'] ?? '', $payload['message'] ?? '');
        } elseif (method_exists($this, 'dispatchBrowserEvent') && is_callable([$this, 'dispatchBrowserEvent'])) {
            $this->dispatchBrowserEvent('toast', $payload);
            $this->dispatchBrowserEvent('showToast', $payload);
        } else {
            session()->flash('toast', $payload);
        }
        $this->resetPage();
    }

    public function handleConfirmedAction($action, $id)
    {
        if ($action === 'restore') {
            $this->restore($id);
        }
    }
}
