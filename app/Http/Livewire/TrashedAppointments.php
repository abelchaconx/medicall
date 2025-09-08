<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointment;

class TrashedAppointments extends Component
{
    use WithPagination;

    public $search = '';
    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    public function render()
    {
        $query = Appointment::onlyTrashed()->with(['patient','doctorMedicalOffice']);
        if ($this->search) {
            $term = '%' . trim($this->search) . '%';
            $query->where('notes', 'like', $term)->orWhereHas('patient', function($q) use ($term) { $q->where('name','like',$term); });
        }

        $appointments = $query->latest()->paginate(12);

        return view('livewire.trashed-appointments', [
            'appointments' => $appointments,
        ]);
    }

    public function confirmAction($action, $id = null)
    {
        if ($action === 'restore') return $this->restore($id);
        if ($action === 'forceDelete') return $this->forceDelete($id);
    }

    public function restore($id)
    {
        $a = Appointment::withTrashed()->find($id);
        if ($a) {
            $a->restore();
            session()->flash('message','Cita restaurada correctamente.');
        }
    }

    public function forceDelete($id)
    {
        $a = Appointment::withTrashed()->find($id);
        if ($a) {
            $a->forceDelete();
            session()->flash('message','Cita eliminada permanentemente.');
        }
    }
}
