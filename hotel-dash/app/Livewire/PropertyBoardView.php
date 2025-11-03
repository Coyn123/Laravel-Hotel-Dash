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
    public $propertyCount;
    public $config;
    public $currentView = 'property-board-view'; // default
    public $targetType = 'pool';
    public $poolLog = [
    'ph' => null,
    'fChlor' => null,
    'cChlor' => null,
    'calc' => null,
    'Alk' => null,
    'cya' => null,
    ];


    public function mount()
    {
        $config = DashboardConfig::get();
        $this->user = auth()->user();

        $firstProperty = collect($config['properties'] ?? [])
                            ->sortBy('property_id')
                            ->first();
        $this->propertyCount = isset($config['properties']) ? count($config['properties']) : 0;

        $this->property = [
            'property_id' => $firstProperty['property_id'] ?? null,
            'property_name' => $firstProperty['property_name'] ?? 'Property Board',
            'auxList' => collect($firstProperty['aux_properties'] ?? [])
                            ->where('property_id', $firstProperty['property_id'] ?? null)
                            ->values()
        ];

        $this->propertyName = $this->property['property_name'];
        $this->loadMessages();
    }

    #[On('togglePropertyBoardView')]
    public function togglePropertyBoard($propertyId): void
    {
        $this->currentView = 'property-board-view';
        $config = DashboardConfig::get();
        $property = collect($config['properties'])->firstWhere('property_id', $propertyId);
        $this->property = [
            'property_id' => $property['property_id'] ?? null,
            'property_name' => $property['property_name'] ?? 'Property Board',
            'auxList' => collect($property['aux_properties'] ?? [])
                            ->where('property_id', $property['property_id'] ?? null)
                            ->values()
        ];
        
        if ($property) {
            $this->property['property_id'] = $propertyId;
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

    #[On('switchAuxView')]
    public function switchAuxView($auxType)
    {
        $this->targetType = strtolower($auxType);

        if (in_array($this->targetType, ['pool', 'spa'])) {
            $this->currentView = 'calender-view-' . $auxType;
        } else {
            $this->currentView = 'property-board-view';
        }
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
            'property_id' => $this->property['property_id'],
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
