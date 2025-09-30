@push('styles')
    @vite(['resources/css/board_style.css'])
@endpush

<div class="panel">
    <div class="panel-header flex justify-between items-center">
        <h2 class="panel-title">Room {{ $room_num }} Board</h2>

        {{-- Room Status Dropdown --}}
        <div>
            <label for="roomStatus" class="text-sm font-semibold mr-2">Status:</label>
            <select id="roomStatus"
                    wire:model="roomStatus"
                    wire:change="updateRoomStatus"
                    class="border rounded p-1 bg-gray-800 text-gray-100">
                <option value="vacant">Vacant</option>
                <option value="occupied">Occupied</option>
                <option value="cleaning">Cleaning</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
    </div>

    <div class="panel-body space-y-4 mt-4">
        @forelse($messages as $message)
            <div class="message {{ $message->flag?->slug ?? '' }}">
                <p>{{ $message->message_text }}</p>
                <div class="message-meta">
                    <span class="flag">{{ $message->flag?->flag_name ?? 'Message' }}</span>
                    <time>{{ $message->created_at->format('Y-m-d H:i') }}</time>
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
                <option value="">-- None --</option>
                @foreach($flags as $flag)
                    <option value="{{ $flag->id }}">{{ $flag->flag_name }}</option>
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