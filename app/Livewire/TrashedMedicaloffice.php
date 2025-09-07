<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicalOffice;

class TrashedMedicaloffice extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    public function render()
    {
        $query = MedicalOffice::onlyTrashed();
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('address_line', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%");
        }

        $medicalOffices = $query->latest()->paginate(12);

        return view('livewire.trashed-medicaloffice', [
            'medicalOffices' => $medicalOffices,
        ]);
    }

    public function confirmAction($action, $id)
    {
        if ($action === 'restore') {
            $this->restore($id);
        } elseif ($action === 'forceDelete') {
            $this->forceDelete($id);
        }
    }

    public function restore($id)
    {
        $medicalOffice = MedicalOffice::withTrashed()->find($id);
        if ($medicalOffice) {
            $medicalOffice->restore();
            session()->flash('message', 'Consultorio médico restaurado exitosamente.');
        }
    }

    public function forceDelete($id)
    {
        $medicalOffice = MedicalOffice::withTrashed()->find($id);
        if ($medicalOffice) {
            $medicalOffice->forceDelete();
            session()->flash('message', 'Consultorio médico eliminado permanentemente.');
        }
    }
}
