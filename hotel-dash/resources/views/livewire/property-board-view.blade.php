<div class="panel flex flex-col max-h-[600px]">
    <!-- Header -->
    <div class="panel-header flex justify-between items-center flex-wrap gap-2">
        <div class="flex items-center gap-3">
            <h2 class="panel-title text-lg font-semibold">
                <button wire:click="togglePropertyBoard('{{ $property['property_id'] }}')">
                    {{ $propertyName }}'s Property Board
                </button>
            </h2>

            <div class="flex flex-wrap gap-2">
                @forelse($property['auxList'] as $aux)
                    <button wire:click="switchAuxView('{{ strtolower($aux['aux_type']) }}')" 
                            class="px-3 py-1 text-sm bg-gray-700 text-white rounded hover:bg-gray-600 transition">
                        {{ $aux['aux_name'] ?? ($aux['aux_type'] ?? 'Auxiliary') }}
                    </button>
                @empty
                    <span class="text-sm italic">No auxiliary properties</span>
                @endforelse
            </div>
        </div>

        @if($propertyCount > 1)
        <button wire:click="toggleCurrentPropertyBoard('{{ $property['property_id'] }}')" class="btn btn-secondary text-xl">
            🔄
        </button>
        @endif
    </div>

    <!-- Scrollable message area -->
    <div class="panel-body flex-1 overflow-y-auto space-y-4 mt-2">
        @if($currentView === 'property-board-view')
            @forelse($messages as $message)
                <div class="message border-b pb-2">
                    <div class="user-in-message font-semibold">{{ $message->user->name ?? 'Unknown User' }}:</div>
                    <div class="message-text">{{ $message->message_text }}</div>
                    <div class="message-meta text-xs text-gray-400">
                        <time>{{ $message->created_at->format('m-d-y H:i') }}</time>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 italic">No messages yet.</p>
            @endforelse
        @elseif(in_array($currentView, ['calender-view-spa', 'calender-view-pool']))
            <livewire:calender-view :target-type="$targetType" :key="'calender-view-' . $targetType" />
        @endif
    </div>

    <!-- Footer inside panel -->
    @if($currentView === 'property-board-view')
    <form wire:submit.prevent="postMessage" class="panel-footer border-t border-[var(--color-border)] mt-2 p-3 flex flex-col gap-2">
        <textarea wire:ignore
                  wire:model.defer="newMessage"
                  placeholder="Type a message..."
                  class="inputText border p-2 w-full rounded-md bg-[var(--color-bg-alt)] text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] resize-none min-h-[60px]">
        </textarea>
        <button type="submit" class="btn btn-primary w-full py-2">Send</button>
    </form>
    @endif
</div>
