<div class="panel">
    <div class="panel-header flex justify-between items-center">
        <h2 class="panel-title">{{ $propertyName }}'s Room {{ $room_num }} Board </h2>
    </div>

    <div class="panel-body space-y-4 mt-4 max-h-96 overflow-y-auto" wire:poll.5s>
        @forelse($messages as $message)
            <div @class([
                'message', // always apply base structure
                'message-urgent' => $message->flag_id == 3,
                'message-work' => $message->flag_id == 2,
                'message-resolved' => $message->flag_id == 4,
            ])>
                <div class="user-in-message">{{ $message->user->name ?? 'Unknown User' }}:</div>
                <div class="message-text">{{ $message->message_text }}</div>

                <div class="message-meta">
                    <span class="flag">{{ $message->flag?->flag_name ?? 'Message' }}</span>
                    <time>{{ $message->created_at->format('m-d-y H:i') }}</time>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No messages yet.</p>
        @endforelse
    </div>

    {{-- Wrap footer in a form --}}
    <form wire:submit.prevent="postMessage" class="panel-footer mt-4 space-y-2">
        {{-- Flag Selector --}}
        <div>
            <label for="flag" class="text-sm font-semibold">Flag:</label>
            <select id="flag"
                    wire:model.defer="selectedFlag"
                    class="border rounded p-1 bg-gray-800 text-gray-100">
                @foreach($flags as $flag)
                    @if($flag->id != 4) {{-- Remove Resolved Tag, only needed for checkbox --}}
                        <option value="{{ $flag->id }}">{{ $flag->flag_name }}</option>
                    @endif
                @endforeach
            </select>
        </div>

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
