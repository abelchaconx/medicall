<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class TrashedUsers extends Component
{
    use WithPagination;

    public $perPage = 10;

    // Use Tailwind for pagination styling
    protected $paginationTheme = 'tailwind';

    protected $listeners = ['confirmAction' => 'handleConfirmedAction'];

    public function render()
    {
    $users = User::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate($this->perPage);
    // Render the Livewire component view (avoid rendering the page that contains the component)
    return view('livewire.trashed-users', compact('users'));
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

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $user->status = 'active';
        $user->save();
    $this->sendToast('green', 'Usuario restaurado');
        $this->resetPage();
    }

    public function handleConfirmedAction($action, $id)
    {
        if ($action === 'restore') {
            $user = User::onlyTrashed()->findOrFail($id);
            if ($user->trashed()) {
                $user->restore();
                $user->status = 'active';
                $user->save();
                $this->sendToast('green', 'Usuario restaurado');
            }
        }

        $this->resetPage();
    }
}
