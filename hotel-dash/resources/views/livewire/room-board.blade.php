<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Room {{ $room }} Board</h2>
    </div>

    <div class="panel-body space-y-4">
        @forelse($messages as $message)
            <div class="p-2 border rounded">
                <div class="flex justify-between items-center">
                    <strong>
                        {{-- Replace with auth()->user()->name if you store user info --}}
                        {{ $message->flag?->flag_name ?? 'Message' }}
                    </strong>
                    <span class="text-xs text-gray-500">
                        {{ $message->created_at->format('Y-m-d H:i') }}
                    </span>
                </div>
                <p>{{ $message->message_text }}</p>
            </div>
        @empty
            <p class="text-gray-500">No messages yet.</p>
        @endforelse
    </div>

    <div class="panel-footer mt-4">
        <input type="text"
               wire:model.defer="newMessage"
               placeholder="Type a message..."
               class="border p-2 w-full" />

        <button wire:click="postMessage"
                class="btn btn-primary mt-2">
            Send
        </button>
    </div>
</div>
