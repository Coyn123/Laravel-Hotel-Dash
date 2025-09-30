<?php

namespace App\Livewire;
use Livewire\Component;
use App\Models\MessageBoard;

class RoomBoard extends Component
{
    public $room;
    public $messages;
    public $newMessage = '';

    public function mount($room)
    {
        $this->room = $room;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = MessageBoard::with('flag')
            ->where('room_id', $this->room)
            ->orderBy('created_at', 'asc')
            ->get();
    }


    public function postMessage()
    {
        if (trim($this->newMessage) === '') {
            return;
        }

        MessageBoard::create([
            'property_id' => 1, // replace with actual property context
            'floor_id'    => 1, // replace with actual floor context
            'room_id'     => $this->room,
            'flag_id'     => 1, // default to "Message"
            'message_text'=> $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }


    public function render()
    {
        return view('livewire.room-board', [
            'messages' => $this->messages,
        ])->layout('layouts.app');
    }
}

