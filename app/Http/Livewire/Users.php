<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;

class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showForm = false;
    public $editingId = null;

    // form fields
    public $name;
    public $email;
    public $password;
    public $role_id;
    // per-row selected role for assigning via table
    public $selectedRole = [];
    public $availableRoles = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'nullable|string|min:6',
        'role_id' => 'nullable|exists:roles,id',
    ];

    protected $updatesQueryString = ['search','perPage','page'];

    protected $listeners = ['refreshUsers' => '$refresh', 'confirmAction' => 'handleConfirmedAction', 'removeRole' => 'removeRole'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function performSearch()
    {
        // Triggered by the search button; deferred input will sync and this will reset pagination
        $this->resetPage();
    }

    /**
     * Clear the search input and show the full listing.
     */
    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function mount()
    {
        $this->perPage = $this->perPage ?: 10;
    // load roles for assign dropdowns
    $this->availableRoles = Role::orderBy('name')->pluck('name','id')->toArray();
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function($q){
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

    // eager-load roles to avoid N+1 when showing assigned roles
    $users = $query->with('roles')->orderBy('id','desc')->paginate($this->perPage);

        return view('livewire.users', compact('users'));
    }

    public function create()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
    // Prefer the first assigned role (many-to-many), fallback to legacy role_id
    $this->role_id = optional($user->roles()->first())->id ?? $user->role_id;
        $this->password = null;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingId) {
            $rules['email'] = ['required','email','max:255', Rule::unique('users','email')->ignore($this->editingId)];
            $rules['password'] = 'nullable|string|min:6';
        }

        $data = $this->validate($rules);

        if ($this->editingId) {
            $user = User::withTrashed()->findOrFail($this->editingId);
            $user->name = $this->name;
            $user->email = $this->email;
            // keep legacy column updated for backward compatibility
            $user->role_id = $this->role_id;
            if ($this->password) $user->password = $this->password;
            $user->save();
            // sync many-to-many roles: when editing via form we replace roles with selected one (or none)
            $user->roles()->sync($this->role_id ? [$this->role_id] : []);
            $this->sendToast('orange', 'Usuario actualizado');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role_id' => $this->role_id,
                'status' => 'active',
            ]);
            // attach role if provided
            if ($this->role_id) {
                $user->roles()->sync([$this->role_id]);
            }
            $this->sendToast('green', 'Usuario creado');
        }

        $this->resetForm();
        $this->showForm = false;
        // Ensure pagination and listing update without relying on emit()
        $this->resetPage();
    }

    /**
     * Assign a role to a user from the table select.
     */
    public function assignRole($userId)
    {
        $selection = $this->selectedRole[$userId] ?? null;
        if (empty($selection)) {
            $this->sendToast('orange', 'Selecciona al menos un rol');
            return;
        }

        $user = User::withTrashed()->findOrFail($userId);

        // normalize selection to array
        $roleIds = is_array($selection) ? array_values(array_filter($selection)) : [ $selection ];
        if (empty($roleIds)) {
            $this->sendToast('orange', 'Selecciona al menos un rol');
            return;
        }

        // validate each id exists
        foreach ($roleIds as $rid) {
            if (! array_key_exists($rid, $this->availableRoles)) {
                $this->sendToast('orange', 'Selecciona un rol válido');
                return;
            }
        }

        try {
            // attach selected roles without removing existing ones
            $user->roles()->syncWithoutDetaching($roleIds);
            // update legacy column to last assigned role for compatibility
            $last = end($roleIds);
            if ($last) {
                $user->role_id = $last;
                $user->save();
            }
            $this->sendToast('green', 'Rol(es) asignado(s)');
        } catch (\Throwable $e) {
            \Log::error('assignRole error', ['user' => $userId, 'roles' => $roleIds, 'error' => $e->getMessage()]);
            $this->sendToast('red', 'Error al asignar rol(es)');
        }

        unset($this->selectedRole[$userId]);
        $this->resetPage();
    }

    /**
     * Remove a role from a user (detach from pivot).
     */
    public function removeRole($userId, $roleId)
    {
        $user = User::withTrashed()->findOrFail($userId);

        // ensure role is currently assigned
        if (! $user->roles()->where('roles.id', $roleId)->exists()) {
            $this->sendToast('orange', 'El usuario no tiene ese rol');
            return;
        }

        try {
            $user->roles()->detach($roleId);

            // if legacy role_id matched removed one, set to another assigned role or null
            if ($user->role_id == $roleId) {
                $new = $user->roles()->first();
                $user->role_id = $new ? $new->id : null;
                $user->save();
            }

            $this->sendToast('green', 'Rol quitado');
        } catch (\Throwable $e) {
            \Log::error('removeRole error', ['user' => $userId, 'role' => $roleId, 'error' => $e->getMessage()]);
            $this->sendToast('red', 'Error al quitar el rol');
        }

        $this->resetPage();
    }

    public function toggleDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->trashed()) {
            $user->restore();
            $user->status = 'active';
            $user->save();
            $this->sendToast('green', 'Usuario restaurado');
        } else {
            // mark as deleted via soft delete and status
            $user->delete();
            $user->status = 'deleted';
            $user->save();
            $this->sendToast('red', 'Usuario eliminado');
        }

        // Ensure updated listing without calling emit()
        $this->resetPage();
    }

    /**
     * Handle confirmations from the client (confirm dialog) and perform
     * the requested action: 'delete' or 'restore'.
     *
     * Called by Livewire when the client emits the 'confirmAction' event.
     */
    public function handleConfirmedAction($action, $id)
    {
        // Support legacy actions: delete, restore and removeRole
        if ($action === 'removeRole') {
            // id is expected as 'userId:roleId'
            if (is_string($id) && strpos($id, ':') !== false) {
                [$userId, $roleId] = explode(':', $id, 2);
                $this->removeRole($userId, $roleId);
            }
            return $this->resetPage();
        }

        $user = User::withTrashed()->findOrFail($id);

        if ($action === 'delete') {
            if (! $user->trashed()) {
                $user->delete();
                $user->status = 'deleted';
                $user->save();
                $this->sendToast('red', 'Usuario eliminado');
            }
        } elseif ($action === 'restore') {
            if ($user->trashed()) {
                $user->restore();
                $user->status = 'active';
                $user->save();
                $this->sendToast('green', 'Usuario restaurado');
            }
        }

        // refresh listing
        $this->resetPage();
    }

    public function show($id)
    {
        // Could be an inline modal or details pane; for now reuse edit with read-only view
        $this->edit($id);
    }

    protected function resetForm()
    {
        $this->name = null;
        $this->email = null;
        $this->password = null;
        $this->role_id = null;
    }

    /**
     * Send a toast to the frontend. Try browser event first, then Livewire
     * emit, then session flash as a last resort.
     */
    protected function sendToast(string $type, string $message)
    {
        $payload = ['type' => $type, 'message' => $message];
        try { \Log::info('Users::sendToast', $payload); } catch (\Throwable $e) {}

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
