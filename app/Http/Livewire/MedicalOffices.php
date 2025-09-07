<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicalOffice;

class MedicalOffices extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $medicalOfficeId;

    public $name;
    public $address_line;
    public $city; // Ahora será departamento
    public $province; // Ahora será provincia del departamento
    public $otros;
    public $phone;
    public $latitude;
    public $longitude;

    // Datos para los selects
    public $departamentos = [];
    public $provincias = [];
    public $provinciasPorDepartamento = [];

    public function mount()
    {
        $this->initializeDepartamentosProvincias();
    }

    protected $listeners = ['confirmAction', 'refreshComponent' => '$refresh'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'address_line' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:120', // Departamento
        'province' => 'nullable|string|max:120', // Provincia
        'otros' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:50',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
    ];

    public function render()
    {
        $query = MedicalOffice::query();
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('address_line', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%")
                  ->orWhere('province', 'like', "%{$this->search}%")
                  ->orWhere('otros', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
        }

        $offices = $query->with(['doctors.user','doctors.medicalOffices'])->latest()->paginate(9);

        return view('livewire.medical-offices', [
            'offices' => $offices,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function initializeDepartamentosProvincias()
    {
        $this->departamentos = [
            'La Paz' => 'La Paz',
            'Cochabamba' => 'Cochabamba', 
            'Santa Cruz' => 'Santa Cruz',
            'Potosí' => 'Potosí',
            'Chuquisaca' => 'Chuquisaca',
            'Tarija' => 'Tarija',
            'Oruro' => 'Oruro',
            'Beni' => 'Beni',
            'Pando' => 'Pando'
        ];

        $this->provinciasPorDepartamento = [
            // ... keep same province mappings as before ...
        ];

        $this->provincias = [];
    }

    public function updatedCity($value)
    {
        $this->province = '';
        if ($value && isset($this->provinciasPorDepartamento[$value])) {
            $this->provincias = $this->provinciasPorDepartamento[$value];
        } else {
            $this->provincias = [];
        }
    }

    public function updateProvincias()
    {
        if ($this->city && isset($this->provinciasPorDepartamento[$this->city])) {
            $this->provincias = $this->provinciasPorDepartamento[$this->city];
        } else {
            $this->provincias = [];
        }
    }
}
