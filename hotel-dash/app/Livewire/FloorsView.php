<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\DashboardConfig;

class FloorsView extends Component
{
    public $search = '';
    public $allProperties = [];
    public $sortFloorsDesc = true; // default: highest → lowest

    public function selectRoom($propertyId, $floorId, $roomId)
    {
    // Emit event to MessageBoard
    $this->dispatch('roomSelected', $propertyId, $floorId, $roomId);
    }

    public function mount()
    {
        $configs = DashboardConfig::get();
        $this->allProperties = $configs['properties'] ?? [];
    }

    public function toggleFloorsSort()
    {
        $this->sortFloorsDesc = ! $this->sortFloorsDesc;
    }

    protected function filteredAndSorted()
    {
        $properties = $this->allProperties;

        // --- Search filter (same as before) ---
        if ($this->search !== '') {
            $search = strtolower($this->search);

            $properties = collect($properties)->map(function ($property) use ($search) {
                $floors = collect($property['floors'])->map(function ($floor) use ($search) {
                    $filteredRooms = collect($floor['rooms'])->filter(function ($room) use ($search) {
                        return str_contains(strtolower($room['room'] ?? ''), $search);
                    });

                    $floor['rooms'] = $filteredRooms->values()->all();
                    $floor['total_rooms'] = count($floor['rooms']);
                    return $floor;
                })->filter(fn($floor) => count($floor['rooms']) > 0)->values()->all();

                if (str_contains(strtolower($property['property_name']), $search) || count($floors) > 0) {
                    $property['floors'] = $floors;
                    return $property;
                }

                return null;
            })->filter()->values()->all();
        }

        // --- Sort by floor count ---
        $properties = collect($properties)->sortBy(
            fn($p) => count($p['floors']),
            SORT_REGULAR,
            $this->sortFloorsDesc // true = descending
        );

        return $properties->values()->toArray();
    }

    public function render()
    {
        return view('livewire.floors-view', [
            'properties' => $this->filteredAndSorted(),
        ]);
    }
}


