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
        // Validate step inputs here (omitted for brevity)
    
        foreach ($this->properties as $propertyIndex => &$propArrays) {
            $propArrays['total_rooms'] = 0;
            $totalRooms = 0;
    
            foreach ($propArrays as $key => $value) {
                if (str_starts_with($key, 'floor_specs_floor')) {
                    $bottom = $value['bottom'] ?? null;
                    $top = $value['top'] ?? null;
                    $increment = $propArrays['increment'] ?? 1;
    
                    if ($bottom !== null && $top !== null) {
                        for ($i = $bottom; $i <= $top; $i += $increment) {
                            $totalRooms++;
                        }
                    }
                }
            }
    
            $propArrays['total_rooms'] = $totalRooms;
        }
        unset($propArrays);

        //Seed
        DB::table('room_statuses')->insert([
            ['id' => 1, 'status_name' => 'Available'],
            ['id' => 2, 'status_name' => 'Occupied'],
            ['id' => 3, 'status_name' => 'Maintenance'],
            ['id' => 4, 'status_name' => 'Out Of Order'],
        ]);
        
        DB::table('room_types')->insert([
            ['id' => 1, 'type_name' => 'Standard'],
            ['id' => 2, 'type_name' => 'Suite'],
        ]);
    
        // Insert each property into DB
        foreach ($this->properties as $propertyIndex => $arrays) {
            // Insert property
            $propertyId = DB::table('properties_config')->insertGetId([
                'property_name'   => $arrays['name'],
                'property_address'=> $arrays['address'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
    
            // Insert floors and rooms
            foreach ($arrays as $arrKey => $values) {
                if (str_starts_with($arrKey, 'floor_specs_floor')) {
                    $floorNum = (int) str_replace('floor_specs_floor', '', $arrKey);
    
                    $floorId = DB::table('floors_config')->insertGetId([
                        'property_id'     => $propertyId,
                        'floor_number'    => $floorNum,
                        'range_start'     => $values['bottom'],
                        'range_end'       => $values['top'],
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
    
                    // Insert rooms for this floor
                    for ($i = $values['bottom']; $i <= $values['top']; $i += ($arrays['increment'] ?? 1)) {
                        DB::table('rooms_config')->insert([
                            'property_id'    => $propertyId,
                            'floor_id'       => $floorId,
                            'room_number'    => $i,
                            'room_type_id'   => 1, // e.g. default to "Standard" (seeded in room_types)
                            'room_status_id' => 1, // e.g. default to "Available" (seeded in room_statuses)
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    }
                }
            }
        }
    
        session()->flash('message', 'Configuration saved successfully.');
    
        return redirect()->route('dashboard');
    }
    
public function render()
    {
        return view('livewire.setup')
        ->layout('layouts.setup', ['title' => 'Initial Setup']);

    }
}
