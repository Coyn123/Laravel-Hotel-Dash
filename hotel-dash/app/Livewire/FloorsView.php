<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\DashboardConfig;

class FloorsView extends Component
{
    public $floors = [];
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public function mount()
    {
        // Pull from your cached service
        $configs = DashboardConfig::get();
        $this->floors = $configs['floors'] ?? [];

        $this->applySort();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->applySort();
    }

    protected function applySort()
    {
        $this->floors = collect($this->floors)
            ->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'desc')
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.floors-view');
    }
}
