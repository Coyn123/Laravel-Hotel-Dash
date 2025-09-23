<?php

namespace App\Livewire;

use Livewire\Component;


class Dashboard extends Component
{
     public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app');
    }


    public $floors = [];

    public function mount()
    {
        $this->floors =
        [
            ['id' => 1, 'name' => 'First Floor', 'start' => 101, 'end' => 110],
            ['id' => 2, 'name' => 'Second Floor', 'start' => 201, 'end' => 210],
        ];
    }
}
