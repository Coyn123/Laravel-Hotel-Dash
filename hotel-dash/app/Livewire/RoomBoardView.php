<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MessagesOnBoard;
use App\Models\MessageFlag;
use App\Models\MessageNotification;
use App\Models\User;
use App\Services\DashboardConfig;
use Livewire\Attributes\On;

class RoomBoardView extends Component
{
    public $property;     // property_id for configuration
    public $propertyName;
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

        // If you have a separate Room model for status, use that instead.
        $this->roomStatus = 'vacant';
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
        $this->roomStatus = 'vacant';
    }

    protected function resolveRoomNumber(): void
    {
        $config = DashboardConfig::get();
        $properties = $config['properties'] ?? [];

        $propertyData = collect($properties)
            ->firstWhere('property_id', $this->property);

        $this->propertyName = $propertyData['property_name'] ?? 'Unknown Property';

        $floorData = collect($propertyData['floors'] ?? [])
            ->firstWhere('id', $this->floor);

        $roomData = collect($floorData['rooms'] ?? [])
            ->firstWhere('id', $this->room);

        $this->room_num = $roomData['room'] ?? null;
    }

    public function loadMessages(): void
    {
        $this->messages = MessagesOnBoard::roomBoard()
            ->with('flag', 'user')
            ->where('room_id', $this->room)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mark these messages as read by the current user
        if ($this->user) {
            $rows = $this->messages->map(fn($msg) => [
                'message_id' => $msg->id,
                'user_id'    => $this->user->id,
                'read_at'    => now(),
            ]);

            MessageNotification::upsert(
                $rows->toArray(),
                ['message_id', 'user_id'],
                ['read_at'],
            );
        }
    }

    public function postMessage(): void
    {
        if (trim($this->newMessage) === '') {
            return;
        }

        $newMessage = MessagesOnBoard::createRoomBoard([
            'user_id' => auth()->id(),
            'property_id' => $this->property,
            'floor_id' => $this->floor,
            'room_id' => $this->room,
            'flag_id' => $this->selectedFlag ?: 1,
            'message_text'=> $this->newMessage,
        ]);

        $allUsers = User::pluck('id');
        $alreadyNotified = MessageNotification::where('message_id', $newMessage->id)
            ->pluck('user_id');
        $usersToInsert = $allUsers->diff($alreadyNotified);

        $rows = $usersToInsert->map(fn($id) => [
            'message_id' => $newMessage->id,
            'user_id'    => $id,
            'read_at'    => $id === auth()->id() ? now() : null,
        ]);

        MessageNotification::insert($rows->toArray());

        $this->reset('newMessage');
        $this->loadMessages();
    }

    public function render()
    {
        $this->resolveRoomNumber();

        return view('livewire.room-board-view', [
            'messages'   => MessagesOnBoard::roomBoard()
                ->with('flag', 'user')
                ->where('room_id', $this->room)
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'floors'     => $this->FullConfig,
            'room_num'   => $this->room_num,
            'flags'      => $this->flags,
            'roomStatus' => $this->roomStatus,
        ])->layout('layouts.app');
    }
}
