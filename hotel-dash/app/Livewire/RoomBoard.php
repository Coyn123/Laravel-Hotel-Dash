<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MessageBoard;
use App\Models\MessageFlag;
use App\Services\DashboardConfig;

class RoomBoard extends Component
{
    public $property;     // property_id from URL
    public $floor;        // floor id from URL
    public $room;         // room id from URL
    public $room_num;     // display number (rooms_config.room_number)

    public $messages;
    public $newMessage = '';
    public $selectedFlag = '';
    public $flags = [];

    public $roomStatus;   // current status of the room
    public $FullConfig = [];

    public function mount($property, $floor, $room)
    {
        $this->property = (int) $property;
        $this->floor    = (int) $floor;
        $this->room     = (int) $room;

        $this->resolveRoomNumber();
        $this->loadMessages();

        // load available flags
        $this->flags = MessageFlag::all();

        // load current room status from DB
        $roomModel = MessageBoard::find($this->room);
        $this->roomStatus = $roomModel?->status ?? 'vacant';
    }

    protected function resolveRoomNumber(): void
    {
        $config = DashboardConfig::get() ?? [];
        $properties = $config['floors'] ?? [];

        $propertyData = collect($properties)
            ->firstWhere('property_id', $this->property);

        $floorData = collect($propertyData['floors'] ?? [])
            ->firstWhere('id', $this->floor);

        $roomData = collect($floorData['rooms'] ?? [])
            ->firstWhere('id', $this->room);

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
            'flag_id'      => $this->selectedFlag ?: null,
            'message_text' => $this->newMessage,
        ]);

        $this->newMessage   = '';
        $this->selectedFlag = '';
        $this->loadMessages();
    }

    public function updateRoomStatus(): void
    {
        $roomModel = MessageBoard::find($this->room);
        if ($roomModel) {
            $roomModel->status = $this->roomStatus;
            $roomModel->save();
        }
    }

    public function render()
    {
        $config = DashboardConfig::get() ?? [];
        $this->FullConfig = $config['floors'] ?? [];

        $this->resolveRoomNumber();

        return view('livewire.room-board', [
            'messages'   => $this->messages,
            'floors'     => $this->FullConfig,
            'room_num'   => $this->room_num,
            'flags'      => $this->flags,
            'roomStatus' => $this->roomStatus,
        ])->layout('layouts.app');
    }
}
