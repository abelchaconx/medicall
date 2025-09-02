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
    // permissions assignment
    public $selectedPermissions = [];
    public $availablePermissions = [];

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['confirmActionRoles' => 'handleConfirmedAction'];


    protected $rules = [
        'name' => 'required|string|max:255',
        'label' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->perPage = $this->perPage ?: 10;
    // load permissions list for selects
    $this->availablePermissions = \App\Models\Permission::orderBy('name')->pluck('name','id')->toArray();
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
        // ensure selectedPermissions is an array for each visible role to avoid binding issues
        foreach ($roles as $role) {
            if (!isset($this->selectedPermissions[$role->id]) || !is_array($this->selectedPermissions[$role->id])) {
                $this->selectedPermissions[$role->id] = [];
            }
        }

        return view('livewire.roles', compact('roles'));
    }

    public function assignPermissions($roleId)
    {
        $ids = $this->selectedPermissions[$roleId] ?? [];
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $ids = array_filter(array_map('intval', $ids));

        $role = Role::findOrFail($roleId);
        // attach new selected permissions without removing existing ones
        if (!empty($ids)) {
            $role->permissions()->syncWithoutDetaching($ids);
            $this->sendToast('green', 'Permiso(s) asignado(s)');
        } else {
            $this->sendToast('orange', 'Selecciona al menos un permiso');
        }
        $this->resetPage();
    }

    public function removePermission($roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);
        $removed = $role->permissions()->detach([$permissionId]);
        if ($removed) {
            $this->sendToast('red', 'Permiso retirado');
        } else {
            $this->sendToast('orange', 'El permiso no estaba asignado');
        }
        $this->resetPage();
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
        // Defensive: sometimes the client emits arrays or nested payloads; normalize to single id
        if (is_array($id)) {
            $id = $id[0] ?? null;
        }
        $id = intval($id);
        if (! $id) {
            $this->sendToast('orange', 'ID inválido para la acción');
            return;
        }

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

    // Accepts action plus optional additional parameters
    public function handleConfirmedAction($action, ...$params)
    {
        // Normalize params: sometimes the client emits nested arrays or detail objects
        $normalize = function ($p) {
            if (is_array($p) && count($p) === 1) return $p[0];
            if (is_array($p) && count($p) > 1) return $p; // keep as array for multi-args
            if (is_object($p)) {
                // handle event.detail or { action, id } shapes
                if (isset($p->id)) return $p->id;
                if (isset($p->args) && is_array($p->args) && count($p->args) === 1) return $p->args[0];
                if (isset($p->detail) && is_array($p->detail) && count($p->detail) === 1) return $p->detail[0];
                if (isset($p->detail) && is_object($p->detail) && isset($p->detail->id)) return $p->detail->id;
            }
            return $p;
        };

        $params = array_map($normalize, $params);

        if (($action === 'delete' || $action === 'restore') && isset($params[0])) {
            $id = is_array($params[0]) ? ($params[0][0] ?? null) : $params[0];
            $this->toggleDelete($id);
            return;
        }

        if ($action === 'removePermission') {
            $roleId = $params[0] ?? null;
            $permissionId = $params[1] ?? null;
            // if roleId is an array [roleId, permId] flatten it
            if (is_array($roleId) && count($roleId) >= 2) {
                $permissionId = $roleId[1];
                $roleId = $roleId[0];
            }
            $roleId = intval($roleId);
            $permissionId = intval($permissionId);
            if ($roleId > 0 && $permissionId > 0) {
                $this->removePermission($roleId, $permissionId);
            }
            return;
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

        // Livewire v3 uses ->dispatch(event, payload), older versions use ->dispatchBrowserEvent
        if (method_exists($this, 'dispatch') && is_callable([$this, 'dispatch'])) {
            // prefer dispatch (v3) - send both object payload and positional args
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

        // Last resort: session flash (visible after full page reload)
        session()->flash('toast', $payload);
    }

    /**
     * Backwards-compatibility: provide a safe emit(...) method so any runtime
     * code that calls $this->emit(...) won't throw a BadMethodCallException.
     * This forwards the event to the browser via dispatchBrowserEvent with the
     * event name as first argument and the rest as payload.
     */
    public function emit(...$params)
    {
        if (empty($params)) {
            return null;
        }

        $event = array_shift($params);
        $payload = null;
        if (count($params) === 1) {
            $payload = $params[0];
        } elseif (count($params) > 1) {
            $payload = $params;
        }

        // Prefer Livewire v3 'dispatch', then 'dispatchBrowserEvent'
        if (method_exists($this, 'dispatch') && is_callable([$this, 'dispatch'])) {
            $this->dispatch($event, $payload ?? []);
        } elseif (method_exists($this, 'dispatchBrowserEvent') && is_callable([$this, 'dispatchBrowserEvent'])) {
            $this->dispatchBrowserEvent($event, $payload ?? []);
        }

        return null;
    }
}
