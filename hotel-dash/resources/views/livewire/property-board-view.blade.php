<div class="panel">
    <div class="panel-header flex justify-between items-center flex-wrap gap-2">
        <div class="flex items-center gap-3">
            <h2 class="panel-title text-lg font-semibold">
                {{ $propertyName }}'s Property Board
            </h2>

            {{-- 🔘 Dynamic auxiliary property tabs --}}
            @php
                $config = \App\Services\DashboardConfig::get();

                // Get the property entry for the current board
                $currentProperty = collect($config['properties'])
                    ->firstWhere('property_id', $propertyId ?? null);

                // Pull its auxiliary properties
                $auxList = collect($currentProperty['aux_properties'] ?? []);
            @endphp

            <div class="flex flex-wrap gap-2">
                @forelse($auxList as $aux)
                    <button
                        wire:click="switchBoard('{{ $aux['aux_name'] ?? 'Unnamed' }}')"
                        class="px-3 py-1 text-sm bg-gray-700 text-white rounded hover:bg-gray-600 transition">
                        {{ $aux['aux_name'] ?? ($aux['aux_type'] ?? 'Auxiliary') }}
                    </button>
                @empty
                    {{-- Optional placeholder if no aux entries exist --}}
                    <span class="text-gray-400 text-sm italic">
                        No auxiliary properties
                    </span>
                @endforelse
            </div>
        </div>

        {{-- 🔄 Property switch button (existing logic) --}}
        @if(count($config['properties']) > 1)
            <button
                wire:click="toggleCurrentPropertyBoard('{{ $property }}')"
                class="btn btn-secondary text-xl">
                🔄
            </button>
        @endif
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
</div>
