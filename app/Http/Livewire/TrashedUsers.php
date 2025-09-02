<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class TrashedUsers extends Component
{
    use WithPagination;

    public $perPage = 10;

    // Use Tailwind for pagination styling
    protected $paginationTheme = 'tailwind';

    public function render()
    {
    $users = User::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate($this->perPage);
    // Render the Livewire component view (avoid rendering the page that contains the component)
    return view('livewire.trashed-users', compact('users'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $user->status = 'active';
        $user->save();
    // Dispatch a Livewire event so the frontend can show a toast (Livewire v3)
    $this->dispatch('toast', ['type' => 'green', 'message' => 'Usuario restaurado']);
    // also set a session flash for full page loads or fallbacks
    session()->flash('toast', ['type' => 'green', 'message' => 'Usuario restaurado']);
        $this->resetPage();
    }
}
