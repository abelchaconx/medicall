<?php

namespace App\Livewire;

use Livewire\Component;

class TestDropdown extends Component
{
    public $city = '';
    public $province = '';
    public $provincias = [];
    
    public $departamentos = [
        'La Paz' => 'La Paz',
        'Cochabamba' => 'Cochabamba',
        'Santa Cruz' => 'Santa Cruz'
    ];
    
    public $provinciasPorDepartamento = [
        'La Paz' => [
            'Pedro Domingo Murillo' => 'Pedro Domingo Murillo',
            'Aroma' => 'Aroma',
            'Pacajes' => 'Pacajes'
        ],
        'Cochabamba' => [
            'Cercado' => 'Cercado',
            'Quillacollo' => 'Quillacollo',
            'Chapare' => 'Chapare'
        ],
        'Santa Cruz' => [
            'Andrés Ibáñez' => 'Andrés Ibáñez',
            'Warnes' => 'Warnes',
            'Sara' => 'Sara'
        ]
    ];

    public function updatedCity($value)
    {
        $this->province = '';
        
        if ($value && isset($this->provinciasPorDepartamento[$value])) {
            $this->provincias = $this->provinciasPorDepartamento[$value];
        } else {
            $this->provincias = [];
        }
    }

    public function render()
    {
        return view('livewire.test-dropdown');
    }
}
