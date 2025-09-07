<?php
namespace App\Livewire;

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
            'La Paz' => [
                'Pedro Domingo Murillo' => 'Pedro Domingo Murillo',
                'Aroma' => 'Aroma',
                'Pacajes' => 'Pacajes',
                'Camacho' => 'Camacho',
                'Muñecas' => 'Muñecas',
                'Larecaja' => 'Larecaja',
                'Franz Tamayo' => 'Franz Tamayo',
                'Ingavi' => 'Ingavi',
                'Loayza' => 'Loayza',
                'Inquisivi' => 'Inquisivi',
                'Sud Yungas' => 'Sud Yungas',
                'Los Andes' => 'Los Andes',
                'Omasuyos' => 'Omasuyos',
                'Nor Yungas' => 'Nor Yungas',
                'Abel Iturralde' => 'Abel Iturralde',
                'Bautista Saavedra' => 'Bautista Saavedra',
                'Manco Kapac' => 'Manco Kapac',
                'Eliodoro Camacho' => 'Eliodoro Camacho',
                'Gualberto Villarroel' => 'Gualberto Villarroel',
                'José Ramón Loayza' => 'José Ramón Loayza'
            ],
            'Cochabamba' => [
                'Cercado' => 'Cercado',
                'Campero' => 'Campero',
                'Ayopaya' => 'Ayopaya',
                'Esteban Arze' => 'Esteban Arze',
                'Arque' => 'Arque',
                'Capinota' => 'Capinota',
                'Germán Jordán' => 'Germán Jordán',
                'Punata' => 'Punata',
                'Tapacarí' => 'Tapacarí',
                'Tiraque' => 'Tiraque',
                'Bolívar' => 'Bolívar',
                'Carrasco' => 'Carrasco',
                'Chapare' => 'Chapare',
                'Mizque' => 'Mizque',
                'Arani' => 'Arani',
                'Quillacollo' => 'Quillacollo'
            ],
            'Santa Cruz' => [
                'Andrés Ibáñez' => 'Andrés Ibáñez',
                'Warnes' => 'Warnes',
                'Velasco' => 'Velasco',
                'Ichilo' => 'Ichilo',
                'Chiquitos' => 'Chiquitos',
                'Sara' => 'Sara',
                'Cordillera' => 'Cordillera',
                'Vallegrande' => 'Vallegrande',
                'Florida' => 'Florida',
                'Ñuflo de Chávez' => 'Ñuflo de Chávez',
                'Obispo Santistevan' => 'Obispo Santistevan',
                'Caballero' => 'Caballero',
                'Germán Busch' => 'Germán Busch',
                'Guarayos' => 'Guarayos',
                'Ángel Sandoval' => 'Ángel Sandoval'
            ],
            'Potosí' => [
                'Tomás Frías' => 'Tomás Frías',
                'Rafael Bustillo' => 'Rafael Bustillo',
                'Cornelio Saavedra' => 'Cornelio Saavedra',
                'Chayanta' => 'Chayanta',
                'Charcas' => 'Charcas',
                'Alonso de Ibáñez' => 'Alonso de Ibáñez',
                'Nor Chichas' => 'Nor Chichas',
                'Sur Chichas' => 'Sur Chichas',
                'Nor Lípez' => 'Nor Lípez',
                'Sur Lípez' => 'Sur Lípez',
                'Antonio Quijarro' => 'Antonio Quijarro',
                'José María Linares' => 'José María Linares',
                'Daniel Campos' => 'Daniel Campos',
                'Modesto Omiste' => 'Modesto Omiste',
                'Enrique Baldivieso' => 'Enrique Baldivieso',
                'Sud Chichas' => 'Sud Chichas'
            ],
            'Chuquisaca' => [
                'Oropeza' => 'Oropeza',
                'Jaime Zudáñez' => 'Jaime Zudáñez',
                'Tomina' => 'Tomina',
                'Hernando Siles' => 'Hernando Siles',
                'Yamparáez' => 'Yamparáez',
                'Nor Cinti' => 'Nor Cinti',
                'Sur Cinti' => 'Sur Cinti',
                'Belisario Boeto' => 'Belisario Boeto',
                'Azurduy' => 'Azurduy',
                'Zudáñez' => 'Zudáñez'
            ],
            'Tarija' => [
                'Cercado' => 'Cercado',
                'Arce' => 'Arce',
                'Gran Chaco' => 'Gran Chaco',
                'Avilés' => 'Avilés',
                'Méndez' => 'Méndez',
                'Burnet O\'Connor' => 'Burnet O\'Connor'
            ],
            'Oruro' => [
                'Cercado' => 'Cercado',
                'Pantaleón Dalence' => 'Pantaleón Dalence',
                'Ladislao Cabrera' => 'Ladislao Cabrera',
                'Avaroa' => 'Avaroa',
                'Carangas' => 'Carangas',
                'Nor Carangas' => 'Nor Carangas',
                'Sur Carangas' => 'Sur Carangas',
                'Sajama' => 'Sajama',
                'Litoral' => 'Litoral',
                'Poopó' => 'Poopó',
                'Tomás Barrón' => 'Tomás Barrón',
                'Sebastián Pagador' => 'Sebastián Pagador',
                'Mejillones' => 'Mejillones',
                'Saucarí' => 'Saucarí',
                'Eduardo Avaroa' => 'Eduardo Avaroa',
                'Atahuallpa' => 'Atahuallpa'
            ],
            'Beni' => [
                'Cercado' => 'Cercado',
                'Vaca Díez' => 'Vaca Díez',
                'José Ballivián' => 'José Ballivián',
                'Yacuma' => 'Yacuma',
                'Moxos' => 'Moxos',
                'Mamoré' => 'Mamoré',
                'Iténez' => 'Iténez',
                'Marbán' => 'Marbán'
            ],
            'Pando' => [
                'Nicolás Suárez' => 'Nicolás Suárez',
                'Manuripi' => 'Manuripi',
                'Madre de Dios' => 'Madre de Dios',
                'Abuna' => 'Abuna',
                'Federico Román' => 'Federico Román'
            ]
        ];

        // No llamar updateProvincias aquí porque no hay city seleccionada aún
        $this->provincias = [];
    }

    public function updatedCity($value)
    {
        $this->province = ''; // Reset province when department changes
        
        // Actualizar provincias directamente en el método
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

    // Método manual para forzar actualización
    public function changeDepartment()
    {
        $this->province = '';
        $this->updateProvincias();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $office = MedicalOffice::withTrashed()->findOrFail($id);
        $this->medicalOfficeId = $office->id;
        $this->name = $office->name;
        $this->address_line = $office->address_line;
        $this->city = $office->city;
        $this->province = $office->province;
        $this->otros = $office->otros;
        $this->phone = $office->phone;
        $this->latitude = $office->latitude;
        $this->longitude = $office->longitude;
        
        // Actualizar provincias basado en el departamento seleccionado
        $this->updateProvincias();
        
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'address_line' => $this->address_line,
            'city' => $this->city,
            'province' => $this->province,
            'otros' => $this->otros,
            'phone' => $this->phone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];

        if ($this->medicalOfficeId) {
            $office = MedicalOffice::withTrashed()->findOrFail($this->medicalOfficeId);
            $office->update($data);
            $this->sendToast('green', 'Consultorio actualizado');
        } else {
            MedicalOffice::create($data);
            $this->sendToast('green', 'Consultorio creado');
        }

        $this->resetForm();
        $this->showForm = false;
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function resetForm()
    {
        $this->medicalOfficeId = null;
        $this->name = '';
        $this->address_line = '';
        $this->city = '';
        $this->province = '';
        $this->otros = '';
        $this->phone = '';
        $this->latitude = null;
        $this->longitude = null;
        $this->provincias = []; // Reset provincias when form is reset
        $this->showForm = false;
    }

    public function performSearch()
    {
        // search is reactive
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
        $office = MedicalOffice::findOrFail($id);
        if (method_exists($office, 'delete')) {
            $office->delete();
            $this->sendToast('orange', 'Consultorio eliminado');
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function restore($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(MedicalOffice::class, 'withTrashed')) {
            $office = MedicalOffice::withTrashed()->findOrFail($id);
            if (method_exists($office, 'restore')) {
                $office->restore();
                $this->sendToast('green', 'Consultorio restaurado');
            }
        }
        try { $this->emit('refreshComponent'); } catch (\Throwable $e) {}
    }

    public function forceDelete($id)
    {
        $id = is_array($id) ? ($id[0] ?? null) : $id;
        if (method_exists(MedicalOffice::class, 'withTrashed')) {
            $office = MedicalOffice::withTrashed()->findOrFail($id);
            if (method_exists($office, 'forceDelete')) {
                $office->forceDelete();
                $this->sendToast('red', 'Consultorio eliminado permanentemente');
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
