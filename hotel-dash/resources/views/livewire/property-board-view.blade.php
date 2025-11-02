<div class="panel">
    <div class="panel-header flex justify-between items-center flex-wrap gap-2">
        <div class="flex items-center gap-3">
            <h2 class="panel-title text-lg font-semibold">
                <button wire:click="togglePropertyBoard('{{ $property['property_id'] }}')">
                    {{ $propertyName }}'s Property Board
                </button>
            </h2>

            <div class="flex flex-wrap gap-2">
                @forelse($property['auxList'] as $aux)
                    <button
                        wire:click="switchAuxView('{{ strtolower($aux['aux_type']) }}')"
                        class="px-3 py-1 text-sm bg-gray-700 text-white rounded hover:bg-gray-600 transition">
                        {{ $aux['aux_name'] ?? ($aux['aux_type'] ?? 'Auxiliary') }}
                    </button>
                @empty
                    <span class="text-sm italic">
                        No auxiliary properties
                    </span>
                @endforelse
            </div>
        </div>

        <button
            wire:click="toggleCurrentPropertyBoard('{{ $property['property_id'] }}')"
            class="btn btn-secondary text-xl">
            🔄
        </button>
    </div>

    <div class="panel-body space-y-4 mt-4 max-h-96 overflow-y-auto" wire:poll.5s>
        @if($currentView === 'property-board-view')
            @forelse($messages as $message)
                <div class="message">
                    <div class="user-in-message">{{ $message->user->name ?? 'Unknown User' }}:</div>
                    <div class="message-text">{{ $message->message_text }}</div>
                    <div class="message-meta">
                        <time>{{ $message->created_at->format('m-d-y H:i') }}</time>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No messages yet.</p>
            @endforelse

            <form wire:submit.prevent="postMessage" class="panel-footer mt-4 space-y-2">
                <textarea
                    wire:ignore
                    wire:model.defer="newMessage"
                    placeholder="Type a message..."
                    class="inputText border p-2">
                </textarea>

                <button type="submit" class="btn btn-primary mt-2">
                    Send
                </button>
            </form>
        @elseif(in_array($currentView, ['calender-view-spa', 'calender-view-pool']))
            <livewire:calender-view 
                :target-type="$targetType" 
                :key="'calender-view-' . $targetType" />
        @endif
    </div>

</div>
