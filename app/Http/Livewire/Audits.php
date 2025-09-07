<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Audit;

class Audits extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $query = Audit::query();
        if ($this->search) {
            $query->where('action', 'like', "%{$this->search}%")
                  ->orWhere('table_name', 'like', "%{$this->search}%");
        }

        $audits = $query->latest('created_at')->paginate(12);

        return view('livewire.audits', [
            'audits' => $audits,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function performSearch() {}

    public function clearSearch()
    {
        $this->search = '';
    }
}
