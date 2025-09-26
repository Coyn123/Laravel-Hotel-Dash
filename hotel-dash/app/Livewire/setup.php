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

        // Default increment if not already set
        if (!isset($this->properties[$propIndex]['increment'])) {
            $this->properties[$propIndex]['increment'] = 1;
        }

        // Initialize each floor spec cleanly
        for ($i = 0; $i < $count; $i++) {
            $key = 'floor_specs_floor' . ($i + 1);

            $this->properties[$propIndex][$key] = [
                'bottom' => $this->properties[$propIndex][$key]['bottom'] ?? null,
                'top'    => $this->properties[$propIndex][$key]['top'] ?? null,
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
            */
            $this->store();
        }

    }

public function store()
    { 
        //  Validation should exist on each step $this->validate
        foreach($this->properties as $property => &$propArrays)
        {
            $propArrays['total_rooms'] = 0;
            $totalRooms = 0;
            foreach($propArrays as $key => $value)
            {
                if(str_starts_with($key, 'floor_specs_floor'))
                {
                    $bottom = $value['bottom'] ?? null;
                    $top = $value['top'] ?? null;
                    $increment = $this->properties[$property]['increment'] ?? 1;

                    if ($bottom !== null && $top !== null) {
                        for ($i = $bottom; $i < $top; $i += $increment) {
                            $totalRooms++;
                        }
                    }
                }
            }
            $propArrays['total_rooms'] = $totalRooms; 
        }
        unset($prop);
        //dd($this->properties);
        
        // Insert each property into DB
        foreach ($this->properties as $property => $arrays) {
            DB::table('properties_config')->insert([
                'property_name' => $arrays['name'],
                'property_address' => $arrays['address'],
                'num_of_floors' => $arrays['total_floors'],
                'num_of_rooms' => $arrays['total_rooms'],
                'incrementation_value' => $arrays['increment'] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            //Insert room values into room config
            foreach ($arrays as $arr => $values)
            {
                if(str_starts_with($arr, 'floor_specs_floor'))
                {
                    $floorNum = str_replace('floor_specs_floor', '', $arr);
                    DB::table('floors_config')->insert([
                        'property_id' => $arrays['name'],
                        'floor_num' => $floorNum,
                        'floor_range_bot' => $values['bottom'],
                        'floor_range_top' => $values['top'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    // Insert from floors specs, each room and its configurations 
                    //(placeholder for now, user determined room configurations needed)
                    for ($i = $values['bottom']; $i <= $values['top']; $i += ($arrays['increment'] ?? 1)) {
                        DB::table('rooms_config')->insert([
                            'property_id' => $arrays['name'],
                            'room_type' => 'standard',
                            'room' => $i,
                            'room_status' => 'placeholder',
                            'floor' => $floorNum,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        session()->flash('message', 'Configuration saved successfully.');

        // Redirect to dashboard after saving
        return redirect()->route('dashboard');
    }

public function render()
    {
        return view('livewire.setup')
        ->layout('layouts.setup', ['title' => 'Initial Setup']);

    }
}
