<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Setup extends Component
{
    public $properties = [];
    public $floors = [];
    public $floorDetails = [];
    public $stepIndex = 0; // 0 = property info, 1 = floors, 2 = floor details, 3 = aux property
    public $currentPropertyIndex = 0;
    public $totalSteps = 4;

    public function mount()
    {
        $existing = DB::table('properties_config')
            ->whereNotNull('property_name')
            ->whereNotNull('property_address')
            ->exists();

        $this->floors[0] = null;
        $this->floorDetails[0] = [];
        $this->addProperty();

        if ($existing) {
            return $this->redirectRoute('dashboard');
        }
    }

    public function addProperty()
    {
        $index = count($this->properties);
        $this->properties[$index] = ['name' => '', 'address' => ''];
        $this->floors[$index] = null;
        $this->floorDetails[$index] = [];       
    }

    public function generateFloorDetails()
    {
        foreach ($this->properties as $propIndex => $prop) {
            $count = (int) ($this->floors[$propIndex] ?? 0);

            $this->properties[$propIndex]['total_floors'] = $count;

            if (! isset($this->properties[$propIndex]['increment'])) {
                $this->properties[$propIndex]['increment'] = 1;
            }

            for ($i = 0; $i < $count; $i++) {
                $key = 'floor_specs_floor' . ($i + 1);

                $this->properties[$propIndex][$key] = [
                    'bottom' => $this->properties[$propIndex][$key]['bottom'] ?? null,
                    'top'    => $this->properties[$propIndex][$key]['top'] ?? null,
                ];
            }
        }
    }

    public function deleteProperty()
    {
        // Only allow deletion if more than one property exists
        if (count($this->properties) > 1) {
            array_pop($this->properties);
            session()->flash('message', 'Last property removed successfully.');
        } else {
            session()->flash('message', 'You must have at least one property.');
        }
    }
    public function addAuxiliary($propertyIndex)
    {
        $this->properties[$propertyIndex]['aux'][] = ['name' => '', 'aux_type' => ''];
    }


    public function nextStep ()
    {
        switch ($this->stepIndex)
        {
            case 0:
                $this->validate([
                    'properties.*.name' => 'required|string|max:255',
                    'properties.*.address' => 'required|string|max:255',
                ]);
                break;
            case 1:
                $this->validate([
                    'floors.*' => 'required|integer|min:1',
                ]);
                $this->generateFloorDetails($this->currentPropertyIndex);
                break;
            case 2:
                foreach($this->floorInfo[0] ?? [] as $floorNum => $details)
                {
                    $this->validate([
                        "floorInfo.*.floor.bottom" => 'required|integer|min:1',
                        "floorInfo.*.floor.top" => 'required|integer|min:1',
                        "floorInfo.*.floor.increment" => 'required|integer|min:1',
                    ]);
                }
                break;
            case 3:
                foreach ($this->properties as $propertyIndex => $property) {
                    foreach ($property['aux'] ?? [] as $auxIndex => $aux) {
                        $this->validate([
                            "properties.$propertyIndex.aux.$auxIndex.name" => 'required|string|max:255',
                            "properties.$propertyIndex.aux.$auxIndex.aux_type" => 'required|string|max:255',
                        ]);
                    }
                }
                break;            
        }

        if ($this->stepIndex < $this->totalSteps - 1) {
            $this->stepIndex++;
        } else {
            $this->store();
        }
    }

    public function store()
    {
        // Compute total rooms for each property config array
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


        // Insert properties, floors, rooms, aux
        foreach ($this->properties as $propertyIndex => $arrays) {
            $propertyId = DB::table('properties_config')->insertGetId([
                'property_name'   => $arrays['name'],
                'property_address'=> $arrays['address'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

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
                        $roomId = DB::table('rooms_config')->insertGetId([
                            'property_id'    => $propertyId,
                            'floor_id'       => $floorId,
                            'room_number'    => $i,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    }
                }
            }
            // Insert auxiliaries
            foreach ($arrays['aux'] ?? [] as $aux) {
                DB::table('aux_property_config')->insert([
                    'property_id' => $propertyId,
                    'aux_name'    => $aux['name'],
                    'aux_type'    => $aux['aux_type'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
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
