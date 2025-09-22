
<div>
    <h1>Initial Setup</h1>

    {{-- Success message --}}
    @if (session()->has('message'))
        <div class="alert alert-success" style="margin-bottom: 1rem;">
            {{ session('message') }}
        </div>
    @endif

    {{-- Livewire form --}}
    <form wire:submit.prevent="store">
        @csrf

        @foreach($floors as $index => $floor)
            <div style="margin-bottom: 1rem; padding: 1rem; border: 1px solid #ccc;">
                <label for="property_name_{{ $index }}">Property Name:</label>
                <input
                    id="property_name_{{ $index }}"
                    type="text"
                    wire:model="floors.{{ $index }}.property_name"
                    required
                >
                @error("floors.$index.property_name")
                    <div class="error" style="color:red;">{{ $message }}</div>
                @enderror

                <label for="floor_name_{{ $index }}" style="margin-left:1rem;">Floor Name:</label>
                <input
                    id="floor_name_{{ $index }}"
                    type="text"
                    wire:model="floors.{{ $index }}.floor_name"
                    required
                >
                @error("floors.$index.floor_name")
                    <div class="error" style="color:red;">{{ $message }}</div>
                @enderror

                @if($index > 0)
                    <button type="button" wire:click="removeFloor({{ $index }})" style="margin-left:1rem;">
                        Remove
                    </button>
                @endif
            </div>
        @endforeach

        <div style="margin-bottom: 1rem;">
            <button type="button" wire:click="addFloor">+ Add Floor</button>
        </div>

        <div>
            <button type="submit">Save Configuration</button>
        </div>
    </form>
</div>
