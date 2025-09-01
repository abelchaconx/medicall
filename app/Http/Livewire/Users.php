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
            $this->dispatch('toast', 'Usuario actualizado');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role_id' => $this->role_id,
                'status' => 'active',
            ]);
            $this->dispatch('toast', 'Usuario creado');
        }

    $this->resetForm();
    $this->showForm = false;
    $this->dispatch('refreshUsers');
    }

    public function toggleDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->trashed()) {
            $user->restore();
            $user->status = 'active';
            $user->save();
            $this->dispatch('toast', 'Usuario restaurado');
        } else {
            // mark as deleted via soft delete and status
            $user->delete();
            $user->status = 'deleted';
            $user->save();
            $this->dispatch('toast', 'Usuario eliminado');
        }

    $this->dispatch('refreshUsers');
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
     * Compatibility shim: some code or older runtime may call emit() or dispatch()
     * directly on the component. Provide simple fallbacks so those calls don't
     * throw BadMethodCallException while still producing a browser event so the
     * front-end can react if needed.
     */
    public function emit($event, ...$params)
    {
        // Fallback implementation that dispatches a browser event when the
        // classic Livewire emit helper isn't available in this runtime.
        if (method_exists($this, 'dispatchBrowserEvent')) {
            $payload = ['params' => $params];
            $this->dispatchBrowserEvent($event, $payload);
        }

        return null;
    }
}
