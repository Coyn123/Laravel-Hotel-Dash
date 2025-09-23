<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Setup extends Component
{
    public $properties = [
        ['property_name' => '', 'floor_name' => '']
    ];

    public function mount()
    {
        // Check if setup data already exists in DB
        $existing = DB::table('properties_config')
            ->whereNotNull('property_name')
            ->whereNotNull('property_address')
            ->exists();

        if ($existing) {
            // Redirect to dashboard if already configured
            return $this->redirectRoute('dashboard');
        }
    }

    public function addProperty ()
    {
        $this->properties[] = ['property_name' => '', 'property_address' => ''];
    }
    public function removeProperty($index)
    {
        unset($this->properties[$index]);
        $this->properties = array_values($this->properties);
    }

    public function store()
    { 

        $this->validate([
            'properties.*.property_name' => 'required|string|max:255',
            'properties.*.property_address'    => 'required|string|max:255',
        ]);

        // Insert each property into DB
        foreach ($this->properties as $property) {
            DB::table('properties_config')->insert([
                'property_name' => $property['property_name'],
                'property_address' => $property['property_address'],
                'num_of_floors' => 0,
                'num_of_rooms' => 0,
                'created_at' => now(),
                'updated_at' => now(),
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
