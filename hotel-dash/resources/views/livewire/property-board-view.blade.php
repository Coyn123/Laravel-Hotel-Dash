<div class="panel">
    <div class="panel-header flex justify-between items-center">
        <h2 class="panel-title">{{ $propertyName }}'s General Property Board</h2>
        {{--- Simple workaround for not showing toggle unneccessarily ---}}
        @php
            $config = \App\Services\DashboardConfig::get();
        @endphp
        @if(count($config['properties']) > 1)
        <button wire:click="toggleCurrentPropertyBoard('{{ $property }}')"
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
