<?php

namespace App\Livewire;
use Livewire\Component;

class RoomBoard extends Component
{
    public $room;

    // Capture the dynamic URL param
    public function mount($room)
    {
        $this->room = $room;
    }

    public function render()
    {
        // Later you can fetch messages/work orders from DB
        $messages = [
            ['user' => 'Admin', 'text' => 'Welcome to room ' . $this->room],
            ['user' => 'Tech', 'text' => 'Work order #123 scheduled.'],
        ];

        return view('livewire.room-board', [
            'messages' => $messages,
        ])
        ->layout('layouts.app');
    }
}
