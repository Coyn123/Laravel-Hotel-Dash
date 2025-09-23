<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Setup extends Component
{
    public $floors = [
        ['property_name' => '', 'floor_name' => '']
    ];

    public function mount()
    {
        // Check if setup data already exists in DB
        $existing = DB::table('configurations')
            ->whereNotNull('property_name')
            ->whereNotNull('floor_name')
            ->exists();

        if ($existing) {
            // Redirect to dashboard if already configured
            return $this->redirectRoute('dashboard');
        }
    }

    public function addFloor ()
    {
        $this->floors[] = ['property_name' => '', 'floor_name' => ''];
    }
    public function removeFloor($index)
    {
        unset($this->floors[$index]);
        $this->floors = array_values($this->floors);
    }

    public function store()
    { 

        $this->validate([
            'floors.*.property_name' => 'required|string|max:255',
            'floors.*.floor_name'    => 'required|string|max:255',
        ]);

        // Insert each floor into DB
        foreach ($this->floors as $floor) {
            DB::table('configurations')->insert([
                'property_name' => $floor['property_name'],
                'floor_name'    => $floor['floor_name'],
                'floor_number'  => 0,
                'floor_count'   => 0,
                'aux_property_count' => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        session()->flash('message', 'Configuration saved successfully.');

        // Redirect to dashboard after saving
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.setup')
        ->layout('layouts.app');

    }
}
