<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
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

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'nullable|string|min:6',
        'role_id' => 'nullable|exists:roles,id',
    ];

    protected $updatesQueryString = ['search','perPage','page'];

    protected $listeners = ['refreshUsers' => '$refresh'];

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

        $users = $query->orderBy('id','desc')->paginate($this->perPage);

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
        $this->role_id = $user->role_id;
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
            $user->role_id = $this->role_id;
            if ($this->password) $user->password = $this->password;
            $user->save();
            $this->sendToast('orange', 'Usuario actualizado');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role_id' => $this->role_id,
                'status' => 'active',
            ]);
            $this->sendToast('green', 'Usuario creado');
        }

        $this->resetForm();
        $this->showForm = false;
        // Ensure pagination and listing update without relying on emit()
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
