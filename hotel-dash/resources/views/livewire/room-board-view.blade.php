<div class="panel flex flex-col max-h-[600px]">
    <!-- Header -->
    <div class="panel-header flex justify-between items-center">
        <h2 class="panel-title">{{ $propertyName }}'s Room {{ $room_num }} Board</h2>
    </div>

    <!-- Scrollable message area -->
    <div class="panel-body flex-1 overflow-y-auto space-y-4 mt-2" wire:poll.3s="loadMessages">
        @forelse($messages as $message)
            <div @class([
                'message', // always apply base structure
                'message-urgent' => $message->flag_id == 3,
                'message-work' => $message->flag_id == 2,
                'message-resolved' => $message->flag_id == 4,
            ])>
                <div class="user-in-message font-semibold">{{ $message->user->name ?? 'Unknown User' }}:</div>
                <div class="message-text">{{ $message->message_text }}</div>
                <div class="message-meta text-xs text-gray-400 flex justify-between">
                    <span class="flag">{{ $message->flag?->flag_name ?? 'Message' }}</span>
                    <time>{{ $message->created_at->format('m-d-y H:i') }}</time>
                </div>
            </div>
        @empty
            <p class="text-gray-500 italic">No messages yet.</p>
        @endforelse
    </div>

    <!-- Footer inside panel -->
    <form wire:submit.prevent="postMessage" class="panel-footer border-t border-[var(--color-border)] mt-2 p-3 flex flex-col gap-2">
        <!-- Flag Selector -->
        <div>
            <label for="flag" class="text-sm font-semibold">Flag:</label>
            <select id="flag"
                    wire:model.defer="selectedFlag"
                    class="border rounded p-1 bg-gray-800 text-gray-100 w-full">
                @foreach($flags as $flag)
                    @if($flag->id != 4) {{-- Remove Resolved Tag --}}
                        <option value="{{ $flag->id }}">{{ $flag->flag_name }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <!-- Message Input -->
        <textarea
            wire:ignore
            wire:model.defer="newMessage"
            placeholder="Type a message..."
            rows="2"
            class="inputText border p-2 w-full rounded-md bg-[var(--color-bg-alt)] text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] resize-none min-h-[60px]">
        </textarea>

        <button type="submit" class="btn btn-primary w-full py-2 mt-2">Send</button>
    </form>
</div>
