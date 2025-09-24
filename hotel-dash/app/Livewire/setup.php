<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Setup extends Component
{   
    public $properties = [];
    public $floors = [];
    public $floorDetails = [];
    public $stepIndex = 0; // 0 = property info, 1 = floors, 2 = floor details
    public $currentPropertyIndex = 0;
    public $totalSteps = 3;

        public function mount()
    {
        // Check if setup data already exists in DB
        $existing = DB::table('properties_config')
            ->whereNotNull('property_name')
            ->whereNotNull('property_address')
            ->exists();
                // At least one property to start
        $this->floors[0] = null;
        $this->floorDetails[0] = [];
        $this->addProperty();

        if ($existing) {
            // Redirect to dashboard if already configured
            return $this->redirectRoute('dashboard');
        }
    }

    public function addProperty()
    {
        $index = count($this->properties);
        $this->properties[$index] = ['name' => '', 'address' => ''];
        $this->floors[$index] = null;
        $this->floorDetails[$index] = [];
        $props = $index + 1;
    }

public function generateFloorDetails()
{
    foreach ($this->properties as $propIndex => $prop) {
        $count = (int) ($this->floors[$propIndex] ?? 0);

        // Store the count directly on the property
        $this->properties[$propIndex]['total_floors'] = $count;
        $this->properties[$propIndex]['increment'] = 1;

        // Initialize floor_specs as an array of floors
        //$this->properties[$propIndex]['floor_specs_floor'] = []

        for ($i = 0; $i < $count; $i++) {
            $key = 'floor_specs_floor' . ($i + 1);
            $this->properties[$propIndex]['floor_specs_floor' . ($i + 1)][$i] = [
                'bottom'    => null,
                'top'       => null,
            ];
        }
    }
}




    public function removeProperty($index)
    {
        unset($this->properties[$index]);
        $this->properties = array_values($this->properties);
    }

    public function nextStep ()
    {
        switch ($this->stepIndex)
        {
            case 0:
                $this->validate([
                    'properties.0.name' => 'required|string|max:255',
                    'properties.0.address' => 'required|string|max:255',
                ]);
                break;
            case 1:
                $this->validate([
                    'floors.0' => 'required|integer|min:1',
                ]);
                $this->generateFloorDetails($this->currentPropertyIndex);
                break;
            case 2:
                foreach($this->floorInfo[0] ?? [] as $floorNum => $details)
                {
                    $this->validate([
                        "floorInfo.0.floor.bottom" => 'required|integer|min:1',
                        "floorInfo.0.floor.top" => 'required|integer|min:1',
                        "floorInfo.0.floor.increment" => 'required|integer|min:1',
                    ]);
                }
                break;
        }

        if ($this->stepIndex < $this->totalSteps - 1) {
            $this->stepIndex++;
        } else {
            /*Final validate and store
                $this->validate([
                "" => 'required|integer|min:1',
                "" => 'required|integer|min:1',
                "" => 'required|integer|min:1',
                "" => 'required|integer|min:1',
                ]);

            $this->store();*/
            dd($this->properties);
        }

    }

    public function store()
    { 
        /*  Validation should exist on each step
        $this->validate([
            'properties.*.property_name' => 'required|string|max:255',
            'properties.*.property_address'    => 'required|string|max:255',
        ]);
        */
        

        // Insert each property into DB
        foreach ($this->properties as $property => $arrays) {
            DB::table('properties_config')->insert([
                'property_name' => $property[$arrays['property_name']],
                'property_address' => $property[$arrays['property_address']],
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
