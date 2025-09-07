<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Permission;

class TrashedPermissions extends Component
{
    use WithPagination;

    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['confirmActionPermissions' => 'handleConfirmedAction'];

    public function render()
    {
        $permissions = Permission::onlyTrashed()->orderBy('id','desc')->paginate($this->perPage);
        return view('livewire.trashed-permissions', compact('permissions'));
    }

    public function restore($id)
    {
        $p = Permission::onlyTrashed()->findOrFail($id);
        $p->restore();
        $this->sendToast('green', 'Permiso restaurado');
        $this->resetPage();
    }

    public function handleConfirmedAction($action, $id)
    {
        if ($action === 'restore') {
            $this->restore($id);
        }
    }

    protected function sendToast(string $type, string $message)
    {
        $payload = ['type' => $type, 'message' => $message];
        try { \Log::info('TrashedPermissions::sendToast', $payload); } catch (\Throwable $e) {}

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
     * Backwards-compatibility: provide a safe emit(...) method so runtime code
     * calling $this->emit(...) won't fail. Forwards event to browser via dispatch.
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

        if (method_exists($this, 'dispatch') && is_callable([$this, 'dispatch'])) {
            $this->dispatch($event, $payload ?? []);
        } elseif (method_exists($this, 'dispatchBrowserEvent') && is_callable([$this, 'dispatchBrowserEvent'])) {
            $this->dispatchBrowserEvent($event, $payload ?? []);
        }

        return null;
    }
}
