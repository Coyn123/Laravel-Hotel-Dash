<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RoomBoardModel;
use App\Models\MessageFlag;
use App\Services\DashboardConfig;
use Livewire\Attributes\On;

class RoomBoardView extends Component
{
    public $property;     // property_id for configuration
    public $floor;        // floor id for configuration
    public $room;         // room id for configuration
    public $room_num;     // display number (rooms_config.room_number)
    public $user;
    public $config;

    public $messages;
    public $newMessage = '';
    public $selectedFlag = '';
    public $flags = [];

    public $roomStatus;   // current status of the room
    public $FullConfig = [];

    #[On('roomSelected')]
    public function loadRoom($propertyId, $floorId, $roomId)
    {
        $this->property = $propertyId;
        $this->floor    = $floorId;
        $this->room     = $roomId;
        $this->resolveRoomNumber();
        $this->loadMessages();
    
        $roomModel = RoomBoardModel::find($roomId);
        $this->roomStatus = $roomModel?->status ?? 'vacant';
    }

    public function mount()
    {
        $config = DashboardConfig::get();
        $this->user = auth()->user();
    
        $firstProperty = collect($config['properties'])->sortBy('property_id')->first();
        $this->property = $firstProperty['property_id'] ?? null;
    
        $firstFloor = collect($firstProperty['floors'])->sortBy('id')->first();
        $this->floor = $firstFloor['id'] ?? null;
    
        $firstRoom = collect($firstFloor['rooms'])->sortBy('id')->first();
        $this->room = $firstRoom['id'] ?? null;
    
        $this->resolveRoomNumber();
        $this->loadMessages();
    
        $this->flags = MessageFlag::all();
    
        $roomModel = RoomBoardModel::find($this->room);
        $this->roomStatus = $roomModel?->status ?? 'vacant';
    }
    
    

    protected function resolveRoomNumber(): void
    {
        $config = DashboardConfig::get();
        $properties = $config['properties'] ?? [];

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
        $this->messages = RoomBoardModel::with('flag', 'user')
            ->where('room_id', $this->room)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function postMessage(): void
    {
        if (trim($this->newMessage) === '') {
            return;
        }

        RoomBoardModel::create([
            'user_id' => auth()->id(),
            'property_id' => $this->property,
            'floor_id' => $this->floor,
            'room_id' => $this->room,
            'flag_id' => $this->selectedFlag ?: 1,
            'message_text' => $this->newMessage,
        ]);

        $this->newMessage   = '';
        $this->selectedFlag = '';
        $this->loadMessages();
    }

    public function updateRoomStatus(): void
    {
        $roomModel = RoomBoardView::find($this->room);
        if ($roomModel) {
            $roomModel->status = $this->roomStatus;
            $roomModel->save();
        }
    }

    public function render()
    {

        $this->resolveRoomNumber();

        return view('livewire.room-board-view', [
            'messages' => $this->messages = RoomBoardModel::with('flag', 'user')
            ->where('room_id', $this->room)
            ->orderBy('created_at', 'desc')
            ->get(),
            'floors'     => $this->FullConfig,
            'room_num'   => $this->room_num,
            'flags'      => $this->flags,
            'roomStatus' => $this->roomStatus,
        ])->layout('layouts.app');
    }
}
