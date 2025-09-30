<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MessageBoard;
use App\Services\DashboardConfig;

class RoomBoard extends Component
{
    public $property;     // property_id from URL
    public $floor;        // floor id from URL
    public $room;         // room id from URL
    public $room_num;     // display number (rooms_config.room_number)
    public $messages;
    public $newMessage = '';
    public $FullConfig = [];

    public function mount($property, $floor, $room)
    {
        $this->property = (int) $property;
        $this->floor    = (int) $floor;
        $this->room     = (int) $room;

        $this->resolveRoomNumber();
        $this->loadMessages();
    }

    protected function resolveRoomNumber(): void
    {
        $config = DashboardConfig::get() ?? [];
        $properties = $config['floors'] ?? [];

        // Find the property
        $propertyData = collect($properties)
            ->firstWhere('property_id', $this->property);

        // Find the floor inside that property
        $floorData = collect($propertyData['floors'] ?? [])
            ->firstWhere('id', $this->floor);

        // Find the room inside that floor
        $roomData = collect($floorData['rooms'] ?? [])
            ->firstWhere('id', $this->room);

        // Store the display number
        $this->room_num = $roomData['room'] ?? null;
    }

    public function loadMessages(): void
    {
        $this->messages = MessageBoard::with('flag')
            ->where('room_id', $this->room)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function postMessage(): void
    {
        if (trim($this->newMessage) === '') {
            return;
        }

        MessageBoard::create([
            'property_id'  => $this->property,
            'floor_id'     => $this->floor,
            'room_id'      => $this->room,
            'flag_id'      => 1,
            'message_text' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function render()
    {
        $config = DashboardConfig::get() ?? [];
        $this->FullConfig = $config['floors'] ?? [];

        $this->resolveRoomNumber();

        return view('livewire.room-board', [
            'messages' => $this->messages,
            'floors'   => $this->FullConfig,
            'room_num' => $this->room_num,
        ])->layout('layouts.app');
    }
}
