<?php

namespace App\Livewire\Header;

use Livewire\Component;
use App\Models\MessageNotification;
use App\Services\DashboardConfig;

class NotificationBox extends Component
{
    public $notifications = [];
    public $open = false; 

    public function mount()
    {
        $this->loadNotifs();
    }
    public function loadNotifs() 
    {
        $user = auth()->user();

        if ($user) {
            $this->notifications = MessageNotification::with(['message.user'])
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->take(5)
            ->get();
        }
    }

    public function toggleOpen()
    {
        $this->open = ! $this->open;
    }

    public function render()
    {
        return view('livewire.header.notification-box', [
            'notifications' => $this->notifications,
        ]);
    }

        public function redirectToNotif($note)
    {
        $prop = (int) $note['property_id'];
        $floor = (int) $note['floor_id'];
        $room = (int) $note['room_id'];
        if(!$prop || ! $floor || ! $room) {
            return;
        }
        $this->dispatch('roomSelected', $prop, $floor, $room);
    }
}
