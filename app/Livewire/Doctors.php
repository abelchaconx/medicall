<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Str;

class Doctors extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 12;
    public $showForm = false;
    public $doctorId;
    public $user_id;
    public $license_number;
    public $bio;
    public $specialty_id;
    public $medical_office_ids = []; // multiple consultorios
    public $availableUsers = [];
    public $availableSpecialties = [];
    public $availableMedicalOffices = [];
    public $trashedCount = 0;

    protected $listeners = ['refreshDoctors' => '$refresh', 'confirmAction' => 'handleConfirmedAction'];

    protected $rules = [
        'user_id' => 'nullable|exists:users,id',
        'license_number' => 'required|string|max:255',
    'bio' => 'nullable|string',
    'specialty_id' => 'nullable|exists:specialties,id',
    'medical_office_ids' => 'nullable|array',
    'medical_office_ids.*' => 'exists:medical_offices,id',
    ];

    public function mount()
    {
        $this->perPage = $this->perPage ?: 12;
        $this->availableUsers = User::orderBy('name')->pluck('name', 'id')->toArray();
    $this->availableSpecialties = \App\Models\Specialty::orderBy('name')->pluck('name','id')->toArray();
    $this->availableMedicalOffices = \App\Models\MedicalOffice::orderBy('name')->pluck('name','id')->toArray();
    $this->trashedCount = Doctor::onlyTrashed()->count();
    }

    public function render()
    {
    $query = Doctor::query()->with(['user','specialties','medicalOffices']);
        if ($this->search) {
            $query->where('license_number', 'like', "%{$this->search}%")
                  ->orWhere('bio', 'like', "%{$this->search}%");
        }

        $doctors = $query->latest()->paginate($this->perPage);

        return view('livewire.doctors', [
            'doctors' => $doctors,
            'availableUsers' => $this->availableUsers,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $doctor = Doctor::withTrashed()->findOrFail($id);
        $this->doctorId = $doctor->id;
        $this->user_id = $doctor->user_id;
        $this->license_number = $doctor->license_number;
        $this->bio = $doctor->bio;
    // load first specialty if exists (form expects single selection)
    $this->specialty_id = optional($doctor->specialties()->first())->id ?? null;
    // load multiple medical offices
    $this->medical_office_ids = $doctor->medicalOffices()->pluck('medical_offices.id')->toArray();
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->doctorId) {
            $doctor = Doctor::withTrashed()->findOrFail($this->doctorId);
            $doctor->update([
                'user_id' => $this->user_id,
                'license_number' => $this->license_number,
                'bio' => $this->bio,
            ]);
            // sync specialty (single)
            if ($this->specialty_id) {
                $doctor->specialties()->sync([$this->specialty_id]);
            } else {
                $doctor->specialties()->detach();
            }
            // sync medical offices (many)
            $doctor->medicalOffices()->sync($this->medical_office_ids ?? []);
            $this->sendToast('green', 'Doctor actualizado');
        } else {
            $doctor = Doctor::create([
                'user_id' => $this->user_id,
                'license_number' => $this->license_number,
                'bio' => $this->bio,
            ]);
            if ($this->specialty_id) {
                $doctor->specialties()->sync([$this->specialty_id]);
            }
            if (!empty($this->medical_office_ids)) {
                $doctor->medicalOffices()->sync($this->medical_office_ids);
            }
            $this->sendToast('green', 'Doctor creado');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->doctorId = null;
        $this->user_id = null;
        $this->license_number = null;
        $this->bio = null;
    $this->specialty_id = null;
    $this->medical_office_ids = [];
        $this->showForm = false;
    }

    public function handleConfirmedAction($action, $id = null)
    {
        // support array/object payloads
        if (is_array($action) && isset($action['action'])) {
            $id = $action['id'] ?? $id;
            $action = $action['action'];
        }

        if ($action === 'delete') return $this->delete($id);
        if ($action === 'restore') return $this->restore($id);
        if ($action === 'forceDelete') return $this->forceDelete($id);
    }

    public function delete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        $doctor = Doctor::findOrFail($id);
        if (method_exists($doctor, 'delete')) {
            $doctor->delete();
            $this->sendToast('orange', 'Doctor eliminado');
        }
    $this->trashedCount = Doctor::onlyTrashed()->count();
    $this->emit('doctorsUpdated');
    $this->resetPage();
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Doctor::class, 'withTrashed')) {
            $doctor = Doctor::withTrashed()->findOrFail($id);
            if (method_exists($doctor, 'restore')) {
                $doctor->restore();
                $this->sendToast('green', 'Doctor restaurado');
            }
    }
    $this->trashedCount = Doctor::onlyTrashed()->count();
    $this->emit('doctorsUpdated');
    $this->resetPage();
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Doctor::class, 'withTrashed')) {
            $doctor = Doctor::withTrashed()->findOrFail($id);
            if (method_exists($doctor, 'forceDelete')) {
                $doctor->forceDelete();
                $this->sendToast('red', 'Doctor eliminado permanentemente');
            }
    }
    $this->trashedCount = Doctor::onlyTrashed()->count();
    $this->emit('doctorsUpdated');
    $this->resetPage();
    }

    protected function sendToast(string $type, string $message)
    {
        $payload = ['type' => $type, 'message' => $message];
        try { \Log::info('Doctors::sendToast', $payload); } catch (\Throwable $e) {}

        if (method_exists($this, 'dispatch') && is_callable([$this, 'dispatch'])) {
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

        session()->flash('toast', $payload);
    }

    public function emit(...$params)
    {
        if (empty($params)) return null;
        $event = array_shift($params);
        $payload = null;
        if (count($params) === 1) $payload = $params[0];
        elseif (count($params) > 1) $payload = $params;

        if (method_exists($this, 'dispatch') && is_callable([$this, 'dispatch'])) {
            $this->dispatch($event, $payload ?? []);
        } elseif (method_exists($this, 'dispatchBrowserEvent') && is_callable([$this, 'dispatchBrowserEvent'])) {
            $this->dispatchBrowserEvent($event, $payload ?? []);
        }

        return null;
    }
}
