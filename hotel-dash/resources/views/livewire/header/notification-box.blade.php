<div class="notification-wrapper relative">
    <button class="notification-btn" wire:click="toggleOpen">
        <span class="icon">🔔</span>
    </button>

    @if($open)
        <div class="notification-box absolute right-0 mt-2 w-64 bg-white shadow-lg rounded">
            <h4 class="notification-title font-semibold p-2 border-b">Notifications</h4>
            <div class="notification-content divide-y divide-gray-200">
                @forelse($notifications as $note)
                <p>
    Debug: property={{ $note->message->property_id }},
    floor={{ $note->message->floor_id }},
    room_id={{ $note->message->room_id }}
</p>

                    <div class="notification-item p-3 hover:bg-gray-100 cursor-pointer transition">
                        <p class="text-sm text-black-800">
                            Room {{ $note->room_number ?? 'N/A' }}: {{ $note->message->message_text }}
                        </p>
                    </div>
                @empty
                    <div class="p-3 text-sm text-gray-500">No notifications</div>
                @endforelse
            </div>
        </div>
    @endif
</div>
