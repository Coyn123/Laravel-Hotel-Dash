<div class="notification-wrapper relative" wire:poll.5s="loadNotifs" wire:click="toggleOpen">
    <button class="notification-btn relative w-full h-full flex items-center justify-center p-2" >
        <span class="icon">🔔</span>

        @if(count($notifications) > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                {{ count($notifications) }}
            </span>
        @endif
    </button>

    @if($open)
    <div class="notification-box absolute right-0 mt-2 w-64 bg-white shadow-lg rounded"
         wire:poll.5s="loadNotifs">
        <h4 class="notification-title font-semibold p-2 border-b">Notifications</h4>
        <div class="notification-content divide-y divide-gray-200">
            @forelse($notifications as $note)
            <div class="notification-item p-3 hover:bg-gray-100 cursor-pointer transition">
                <button wire:click="redirectToNotif(@js($note->message))"
                class="text-sm text-black-800 w-full h-full flex text-center justify-left p-2 
                text-center truncate">
                    {{ $note->message->message_text }}
                </button>
            </div>
            @empty
                <div class="p-3 text-sm text-gray-500">No notifications</div>
            @endforelse
        </div>
    </div>
@endif
</div>
