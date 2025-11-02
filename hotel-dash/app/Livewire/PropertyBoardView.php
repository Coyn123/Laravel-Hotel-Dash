<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MessagesOnBoard;
use App\Models\MessageNotification;
use App\Models\User;
use App\Services\DashboardConfig;
use Livewire\Attributes\On;

class PropertyBoardView extends Component
{ 
    public $user;
    public $messages;
    public $property;
    public $propertyName;
    public $newMessage = '';
    public $config;

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

        if ($currentIndex === false) {
            $nextPropertyId = $propertyIds->first();
        } else {
            $nextIndex = $currentIndex + 1;
            if ($nextIndex >= $propertyIds->count()) {
                $nextIndex = 0;
            }
            $nextPropertyId = $propertyIds->get($nextIndex);
        }

        if ($nextPropertyId !== null) {
            $this->togglePropertyBoard($nextPropertyId);
        }
    }
    public function switchBoard($target)
    {
        // Handle switching logic here, e.g.:
        //$this->currentBoard = $target;
        // Optionally refresh messages or context for that aux property
    }


    public function loadMessages(): void
    {
        $this->messages = MessagesOnBoard::propertyBoard()
            ->with('flag', 'user')
            ->where('property_id', $this->property)
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
                ['read_at']
            );
        }
    }

    public function postMessage(): void
    {
        if (trim($this->newMessage) === '') {
            return;
        }

        $newMessage = MessagesOnBoard::createPropertyBoard([
            'user_id'     => auth()->id(),
            'property_id' => $this->property,
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
        return view('livewire.property-board-view', [
            'messages' => MessagesOnBoard::propertyBoard()
                ->with('flag', 'user')
                ->where('property_id', $this->property)
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'property' => $this->property,
        ])->layout('layouts.app');
    }
}
