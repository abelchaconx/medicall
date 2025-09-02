<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;
use Illuminate\Validation\Rule;

class Roles extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showForm = false;
    public $editingId = null;

    public $name;
    public $label;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['confirmActionRoles' => 'handleConfirmedAction'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'label' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->perPage = $this->perPage ?: 10;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function performSearch()
    {
        // sync deferred input then reset pagination
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Role::query();
        if ($this->search) {
            $query->where('name','like','%'.$this->search.'%')
                  ->orWhere('label','like','%'.$this->search.'%');
        }

        $roles = $query->orderBy('id','desc')->paginate($this->perPage);
        return view('livewire.roles', compact('roles'));
    }

    public function create()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $role = Role::withTrashed()->findOrFail($id);
        $this->editingId = $role->id;
        $this->name = $role->name;
        $this->label = $role->label;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingId) {
            $rules['name'] = ['required','string','max:255', Rule::unique('roles','name')->ignore($this->editingId)];
        }

        $data = $this->validate($rules);

        if ($this->editingId) {
            $role = Role::withTrashed()->findOrFail($this->editingId);
            $role->name = $this->name;
            $role->label = $this->label;
            $role->save();
            $this->sendToast('orange', 'Rol actualizado');
        } else {
            Role::create([
                'name' => $this->name,
                'label' => $this->label,
            ]);
            $this->sendToast('green', 'Rol creado');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function toggleDelete($id)
    {
        $role = Role::withTrashed()->findOrFail($id);
        if ($role->trashed()) {
            $role->restore();
            $role->save();
            $this->sendToast('green','Rol restaurado');
        } else {
            $role->delete();
            $this->sendToast('red','Rol eliminado');
        }
        $this->resetPage();
    }

    public function handleConfirmedAction($action, $id)
    {
        if ($action === 'delete') {
            $this->toggleDelete($id);
        } elseif ($action === 'restore') {
            $this->toggleDelete($id);
        }
    }

    protected function resetForm()
    {
        $this->name = null;
        $this->label = null;
    }

    protected function sendToast(string $type, string $message)
    {
        $payload = ['type' => $type, 'message' => $message];
        try { \Log::info('Roles::sendToast', $payload); } catch (\Throwable $e) {}

        if (method_exists($this, 'dispatch') && is_callable([$this, 'dispatch'])) {
            $this->dispatch('toast', $payload);
            $this->dispatch('toast', $payload['type'] ?? '', $payload['message'] ?? '');
            $this->dispatch('showToast', $payload);
            $this->dispatch('showToast', $payload['type'] ?? '', $payload['message'] ?? '');
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
