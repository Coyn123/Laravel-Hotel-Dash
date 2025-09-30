{{-- resources/views/livewire/setup.blade.php --}}
<div>
    <h1>Initial Setup</h1>

    {{-- Success message --}}
    @if (session()->has('message'))
        <div class="alert alert-success mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Livewire form --}}
    <form wire:submit.prevent="nextStep">
        <div class="property-block">
            {{-- Step 0: Property Info --}}
            @if($stepIndex === 0)
                @foreach($properties as $propIndex => $prop)
                    <div class="mb-4 p-4 border rounded bg-gray-50">
                        <input 
                            wire:model="properties.{{ $propIndex }}.name" 
                            placeholder="Property Name" 
                            class="block w-full mb-2 border rounded p-2"
                        >
                        <input 
                            wire:model="properties.{{ $propIndex }}.address" 
                            placeholder="Address" 
                            class="block w-full mb-2 border rounded p-2"
                        >
                    </div>
                @endforeach
                <button type="button" wire:click="addProperty" class="btn-secondary">
                    Add Property
                </button>
                @if(count($properties) > 1)
                    <button type="button" wire:click="deleteProperty" class="btn-danger ml-2">
                        Delete Property
                    </button>
                @endif
            @endif

            {{-- Step 1: Floors --}}
            @if($stepIndex === 1)
                @foreach($properties as $propIndex => $prop)
                    <div class="mb-4 p-4 border rounded bg-gray-50">
                        <label class="block font-semibold mb-2">
                            Number of floors for: {{ $prop['name'] ?: 'Property ' . ($propIndex + 1) }}
                        </label>
                        <input 
                            type="number" 
                            wire:model="floors.{{ $propIndex }}"
                            placeholder="Total Floors" 
                            class="block w-full border rounded p-2"
                        >
                    </div>
                @endforeach
            @endif

            {{-- Step 2: Floor details --}}
            @if($stepIndex === 2)
                @foreach($properties as $propIndex => $prop)
                    <div class="mb-4 p-4 border rounded bg-gray-50">
                        <h3 class="font-semibold mb-2">{{ $prop['name'] ?: 'Property ' . ($propIndex + 1) }}</h3>

                        @foreach($prop as $key => $details)
                            @if(\Illuminate\Support\Str::startsWith($key, 'floor_specs_floor') && is_array($details))
                                @if(array_key_exists('bottom', $details) && array_key_exists('top', $details))
                                    <div class="mb-3 pl-3 border-l-4 border-gray-300">
                                        <strong>
                                            Floor Range for Floor: {{ (int) str_replace('floor_specs_floor', '', $key) }}
                                        </strong>
                                        <div class="flex gap-2 mt-2">
                                            <input 
                                                type="number" 
                                                wire:model="properties.{{ $propIndex }}.{{ $key }}.bottom" 
                                                placeholder="Start Room"
                                                class="flex-1 border rounded p-2"
                                            >
                                            <input 
                                                type="number" 
                                                wire:model="properties.{{ $propIndex }}.{{ $key }}.top" 
                                                placeholder="End Room"
                                                class="flex-1 border rounded p-2"
                                            >
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach

                        <div>
                            <strong>Incrementation</strong>
                            <select 
                                wire:model="properties.{{ $propIndex }}.increment"
                                class="block w-full border rounded p-2 mt-1"
                            >
                                <option value="1">1</option>
                                <option value="10">10</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <button type="submit" class="btn-primary mt-4">
            {{ ($stepIndex < $totalSteps - 1) ? 'Next' : 'Submit' }}
        </button>
    </form>
</div>
