<div class="panel">
    <div class="panel-header flex justify-between items-center">
        <h2 class="panel-title">{{ $propertyName }}</h2>
    </div>

    <div class="panel-body space-y-4 mt-4 max-h-96 overflow-y-auto" wire:poll.5s>
        @forelse($messages as $message)
            <div @class([
                'message',
            ])>
                <div class="user-in-message">{{ $message->user->name ?? 'Unknown User' }}:</div>
                <div class="message-text">{{ $message->message_text }}</div>

                <div class="message-meta">
                    <time>{{ $message->created_at->format('m-d-y H:i') }}</time>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No messages yet.</p>
        @endforelse
    </div>

    {{-- Wrap footer in a form --}}
    <form wire:submit.prevent="postMessage" class="panel-footer mt-4 space-y-2">

        {{-- Message Input --}}
        <input type="text"
               wire:model.defer="newMessage"
               placeholder="Type a message..."
               class="border p-2 w-full bg-gray-900 text-gray-100" />

        <button type="submit" class="btn btn-primary mt-2">
            Send
        </button>
    </form>
</div>
