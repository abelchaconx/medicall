<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Patients extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $patientId;
    public $birthdate;
    public $phone;
    public $notes;
    public $user_id;
    public $gender;
    // create user inline
    public $createUser = false;
    public $user_name;
    public $user_email;
    public $user_password;
    public $user_password_confirmation;
    // quick-associate UI
    public $associateUserId;

    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    protected $rules = [
        'user_id' => 'nullable|exists:users,id',
    'associateUserId' => 'nullable|exists:users,id',
        'gender' => 'nullable|in:male,female,other',
        'birthdate' => 'nullable|date',
        'phone' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        // allow user-related fields to be present but validate them dynamically in save()
        'user_name' => 'nullable|string|max:255',
        'user_email' => 'nullable|email',
        'user_password' => 'nullable|confirmed|min:8',
    ];

    public function render()
    {
        $query = Patient::query()->with('user');
        if ($this->search) {
            $query->where('phone', 'like', "%{$this->search}%")
                  ->orWhere('notes', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function($q){
                      $q->where('name', 'like', "%{$this->search}%");
                  });
        }

        $patients = $query->latest()->paginate(12);

        $availableUsers = \App\Models\User::orderBy('name')->get()->mapWithKeys(function($u){ return [$u->id => $u->name ?? ('User #' . $u->id)]; });

        return view('livewire.patients', [
            'patients' => $patients,
            'availableUsers' => $availableUsers,
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
        $patient = Patient::withTrashed()->findOrFail($id);
        $this->patientId = $patient->id;
        // basic patient fields
        $this->birthdate = $patient->birthdate?->toDateString() ?? null;
        $this->phone = $patient->phone ?? null;
        $this->notes = $patient->notes ?? null;
        $this->gender = $patient->gender ?? null;

        // If the patient has a linked user, show the "existing user" select
        // and prefill the select + convenience name/email fields.
        if ($patient->user) {
            $this->createUser = true;
            $this->user_id = $patient->user_id;
            $this->user_name = $patient->user->name ?? null;
            $this->user_email = $patient->user->email ?? null;
        } else {
            // no linked user -> default to inline-create mode
            $this->createUser = false;
            $this->user_id = null;
            $this->user_name = null;
            $this->user_email = null;
        }

        $this->showForm = true;
    }

    public function save()
    {
        // merge base rules with conditional user creation rules
        $rules = $this->rules;
        // If createUser is false we are in "inline create user" mode and must
        // validate the inline user fields (require name/email/password).
        if (! $this->createUser) {
            $rules = array_merge($rules, [
                'user_name' => 'required|string|max:255',
                'user_email' => 'required|email|unique:users,email',
                'user_password' => 'required|confirmed|min:8',
            ]);
        } else {
            // If we're associating an existing user while editing, allow updating
            // the linked user's name/email. Use unique rule that ignores the
            // current user's id to avoid false positives.
            if ($this->patientId && $this->user_id) {
                $rules = array_merge($rules, [
                    'user_name' => 'required|string|max:255',
                    'user_email' => 'required|email|unique:users,email,' . $this->user_id,
                ]);
            }
        }

        $this->validate($rules);

        try {
            DB::transaction(function () {
                // create user if in inline-create mode (createUser == false)
                if (! $this->createUser) {
                    $user = User::create([
                        'name' => $this->user_name,
                        'email' => $this->user_email,
                        'password' => Hash::make($this->user_password),
                    ]);
                    $this->user_id = $user->id;
                }

                if ($this->patientId) {
                    $patient = Patient::withTrashed()->findOrFail($this->patientId);
                    $patient->update([
                        'user_id' => $this->user_id,
                        'birthdate' => $this->birthdate,
                        'phone' => $this->phone,
                        'notes' => $this->notes,
                        'gender' => $this->gender,
                    ]);
                    $this->sendToast('green', 'Paciente actualizado');
                    // If we're linking to an existing user and provided name/email,
                    // update the user record accordingly.
                    if ($this->createUser && $this->user_id) {
                        $u = User::find($this->user_id);
                        if ($u) {
                            $update = [];
                            if ($this->user_name && $this->user_name !== $u->name) $update['name'] = $this->user_name;
                            if ($this->user_email && $this->user_email !== $u->email) $update['email'] = $this->user_email;
                            if (! empty($update)) {
                                $u->update($update);
                            }
                        }
                    }
                } else {
                    Patient::create([
                        'user_id' => $this->user_id,
                        'birthdate' => $this->birthdate,
                        'phone' => $this->phone,
                        'notes' => $this->notes,
                        'gender' => $this->gender,
                    ]);
                    $this->sendToast('green', 'Paciente creado');
                }
            });
        } catch (\Throwable $e) {
            // Surface the error for faster debugging in dev; log it and show a toast.
            try { \Log::error('Patients::save error - ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]); } catch (\Throwable $_) {}
            $this->sendToast('red', 'Error guardando paciente: ' . ($e->getMessage() ?? 'ver logs'));
            return;
        }

        $this->resetForm();
        $this->showForm = false;
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->patientId = null;
        $this->birthdate = null;
        $this->phone = null;
        $this->notes = null;
        $this->user_id = null;
        $this->showForm = false;
    $this->gender = null;
    // clear create-user fields
    $this->createUser = false;
    $this->user_name = null;
    $this->user_email = null;
    $this->user_password = null;
    $this->user_password_confirmation = null;
    }

    /**
     * When the createUser checkbox is toggled on, clear any selected user_id
     * so the inline creation starts from a clean state.
     */
    public function updatedCreateUser($value)
    {
        // When checkbox becomes true => using existing user: clear password
        // fields and (if a user is selected) populate name/email from that user.
        // When checkbox becomes false => inline-create: clear selected user id
        // and clear name/email so a new user can be created.
        if ($value) {
            $this->user_password = null;
            $this->user_password_confirmation = null;
            if ($this->user_id) {
                $u = User::find($this->user_id);
                $this->user_name = $u->name ?? null;
                $this->user_email = $u->email ?? null;
            }
        } else {
            $this->user_id = null;
            $this->user_name = null;
            $this->user_email = null;
            $this->user_password = null;
            $this->user_password_confirmation = null;
        }
    }

    public function updatedUserId($value)
    {
        // When selecting an existing user from the select, populate the
        // name/email fields so they can be edited without requiring password.
        if ($value) {
            $u = User::find($value);
            $this->user_name = $u->name ?? null;
            $this->user_email = $u->email ?? null;
        } else {
            $this->user_name = null;
            $this->user_email = null;
        }
    }

    // Quick associate helpers removed — feature deprecated from UI
    // Re-introduced to support tests and small admin flows
    public function openAssociate($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $this->patientId = $patient->id;
        $this->associateUserId = $patient->user_id;
        // keep the main form closed; this is a quick modal-like flow in UI
        $this->showForm = false;
    }

    public function associateSave()
    {
        $this->validate([
            'associateUserId' => 'required|exists:users,id',
        ]);

        $patient = Patient::findOrFail($this->patientId);
        $patient->update(['user_id' => $this->associateUserId]);

        $this->sendToast('green', 'Usuario asociado al paciente');
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function performSearch()
    {
        // search is livewire-bound
    }

    public function clearSearch()
    {
        $this->search = '';
    }

    public function confirmAction($action, $id = null)
    {
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
        $patient = Patient::findOrFail($id);
        if (method_exists($patient, 'delete')) {
            $patient->delete();
            $this->sendToast('orange', 'Paciente eliminado');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Patient::class, 'withTrashed')) {
            $patient = Patient::withTrashed()->findOrFail($id);
            if (method_exists($patient, 'restore')) {
                $patient->restore();
                $this->sendToast('green', 'Paciente restaurado');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(Patient::class, 'withTrashed')) {
            $patient = Patient::withTrashed()->findOrFail($id);
            if (method_exists($patient, 'forceDelete')) {
                $patient->forceDelete();
                $this->sendToast('red', 'Paciente eliminado permanentemente');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    protected function sendToast($type, $message)
    {
        if (method_exists($this, 'dispatch')) {
            try { $this->dispatch('toast', ['type' => $type, 'message' => $message]); return; } catch (\Throwable $e) { }
        }

        if (method_exists($this, 'dispatchBrowserEvent')) {
            $this->dispatchBrowserEvent('toast', ['type' => $type, 'message' => $message]);
            return;
        }

        session()->flash('toast', ['type' => $type, 'message' => $message]);
    }
}
