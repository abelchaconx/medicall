<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Permission;
use Illuminate\Validation\Rule;

class Permissions extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showForm = false;
    public $editingId = null;

    public $name;
    public $label;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['confirmActionPermissions' => 'handleConfirmedAction'];

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
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Permission::query();
        if ($this->search) {
            $query->where('name','like','%'.$this->search.'%')
                  ->orWhere('label','like','%'.$this->search.'%');
        }

        $permissions = $query->orderBy('id','desc')->paginate($this->perPage);
        return view('livewire.permissions', compact('permissions'));
    }

    public function create()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $perm = Permission::withTrashed()->findOrFail($id);
        $this->editingId = $perm->id;
        $this->name = $perm->name;
        $this->label = $perm->label;
        $this->showForm = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingId) {
            $rules['name'] = ['required','string','max:255', Rule::unique('permissions','name')->ignore($this->editingId)];
        }

        $data = $this->validate($rules);

        if ($this->editingId) {
            $perm = Permission::withTrashed()->findOrFail($this->editingId);
            $perm->name = $this->name;
            $perm->label = $this->label;
            $perm->save();
            $this->sendToast('orange', 'Permiso actualizado');
        } else {
            Permission::create([
                'name' => $this->name,
                'label' => $this->label,
            ]);
            $this->sendToast('green', 'Permiso creado');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function toggleDelete($id)
    {
        if (is_array($id)) {
            $id = $id[0] ?? null;
        }
        $id = intval($id);
        if (! $id) {
            $this->sendToast('orange', 'ID inválido para la acción');
            return;
        }

        $perm = Permission::withTrashed()->findOrFail($id);
        if ($perm->trashed()) {
            $perm->restore();
            $perm->save();
            $this->sendToast('green','Permiso restaurado');
        } else {
            $perm->delete();
            $this->sendToast('red','Permiso eliminado');
        }
        $this->resetPage();
    }

    public function handleConfirmedAction($action, $id)
    {
        if ($action === 'delete' || $action === 'restore') {
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
        try { \Log::info('Permissions::sendToast', $payload); } catch (\Throwable $e) {}

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
    public function emit($event = null, ...$params)
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
