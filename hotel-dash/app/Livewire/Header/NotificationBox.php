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
        $user = auth()->user();

        if ($user) {
            $this->notifications = MessageNotification::with(['message.user'])
            ->where('user_id', $user->id)
            ->orderBy('read_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($note) {
                $note->room_number = DashboardConfig::resolveRoomNumberbyID(
                    $note->message->room_id,
                );
                return $note;
            });
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
}
