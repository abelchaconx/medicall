<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class SmartTable extends Component
{
    use WithPagination;

    public $modelClass;
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $updatesQueryString = ['search','sortField','sortDirection','perPage'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        if (!class_exists($this->modelClass)) {
            return view('livewire.smart-table', ['rows' => collect()]);
        }

        $model = new $this->modelClass;
        $query = $model->newQuery();

        if ($this->search) {
            // naive search across string columns; adapter can override
            $query->where(function($q){
                foreach ($this->searchableColumns() as $col) {
                    $q->orWhere($col, 'like', '%'.$this->search.'%');
                }
            });
        }

        $rows = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('livewire.smart-table', compact('rows'));
    }

    protected function searchableColumns()
    {
        // default: try common columns
        return ['name','title','email','phone'];
    }
}
