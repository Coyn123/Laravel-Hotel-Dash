<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PropertyBoardModel;
use App\Models\MessageFlag;
use App\Services\DashboardConfig;
use Livewire\Attributes\On;


class PropertyBoardView extends Component
{
    public $config;
    public $user;
    public $messages;
    public $property;
    public $propertyName;
    public $newMessage = '';

    public function mount() 
    {
        $config = DashboardConfig::get();
        $this->user = auth()->user();

        $firstProperty = collect($config['properties'])->sortBy('property_id')->first();
        $this->property = $firstProperty['property_id'] ?? null;
        $this->propertyName = $firstProperty['property_name'] ?? 'Property Board';

        $this->loadMessages();
    }

    #[On('togglePropertyBoardView')]
    public function togglePropertyBoard($propertyId): void
    {
        $config = DashboardConfig::get();
        $property = collect($config['properties'])->firstWhere('property_id', $propertyId);

        if ($property) {
            $this->property = $propertyId;
            $this->propertyName = $property['property_name'] ?? 'Property Board';
            $this->loadMessages();
        }
    }

    public function toggleCurrentPropertyBoard($property): void
    {
        $config = DashboardConfig::get();
        $propertyIds = collect($config['properties'])->pluck('property_id')->sort()->values();

        if (is_int($property) && $property >= 0 && $property < $propertyIds->count() && ! $propertyIds->contains($property)) {
            $currentIndex = $property;
        } else {
            $currentIndex = $propertyIds->search($property);
        }

        // If not found, default to first property
        if ($currentIndex === false) {
            $nextPropertyId = $propertyIds->first();
        } else {
            $nextIndex = $currentIndex + 1;
            if ($nextIndex >= $propertyIds->count()) {
                $nextIndex = 0; // wrap to first
            }
            $nextPropertyId = $propertyIds->get($nextIndex);
        }

        if ($nextPropertyId !== null) {
            $this->togglePropertyBoard($nextPropertyId);
        }
    }

    public function loadMessages(): void
    {
        $this->messages = PropertyBoardModel::with('flag', 'user')
            ->where('property_id', $this->property)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function postMessage(): void
    {
        if (trim($this->newMessage) === '') {
            return;
        }

        PropertyBoardModel::create([
            'user_id' => auth()->id(),
            'property_id' => $this->property,
            'message_text' => $this->newMessage,
        ]);

        $this->newMessage   = '';
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.property-board-view', [
            'messages' => $this->messages = PropertyBoardModel::with('flag', 'user')
            ->where('property_id', $this->property)
            ->orderBy('created_at', 'desc')
            ->get(),
            'property'   => $this->property,
        ])->layout('layouts.app');
    }
}