<?php

namespace App\Livewire\Header;

use Livewire\Component;

class NotificationBox extends Component
{
    public $notifications = [];
    public $open = false; 

    public function mount()
    {
        // Testing with array below, we want eloquent database models
        $this->notifications = [["message" => "Message Testing"],["message" => "Message Testing 2"]];
    }
        public function toggleOpen()
    {
        $this->open = ! $this->open;
    }

    public function render()
    {
        return view('livewire.header.notification-box');
    }
}

