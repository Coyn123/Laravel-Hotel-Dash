<div>
    @csrf
    <h1>Initial Setup</h1>

    {{-- Success message --}}
    @if (session()->has('message'))
        <div class="alert alert-success" style="margin-bottom: 1rem;">
            {{ session('message') }}
        </div>
    @endif

    {{-- Debug info (remove in production) --}}
    <pre>
Step: {{ $stepIndex }}
Properties: {{ json_encode($properties) }}
    </pre>

    {{-- Livewire form --}}
    <form wire:submit.prevent="nextStep">

        {{-- Only show the current property being edited --}}
        @php $pIndex = $currentPropertyIndex; @endphp

        <div class="property-block">
            {{-- Step 0: Property Info --}}
            @if($stepIndex === 0)
                @foreach($properties as $propIndex => $prop)
                    <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; background-color: #f9f9f9;">
                        <input 
                            wire:model="properties.{{ $propIndex }}.name" 
                            placeholder="Property Name" 
                            style="display: block; width: 100%; margin-bottom: 0.5rem;"
                        >
                        <input 
                            wire:model="properties.{{ $propIndex }}.address" 
                            placeholder="Address" 
                            style="display: block; width: 100%; margin-bottom: 0.5rem;"
                        >
                @endforeach
        </div>
        <div>
        {{-- Add Property button only on step 0 --}}
            <button type="button" wire:click="addProperty">Add Property</button>
        </div>
            @endif

            {{-- Step 1: Floors --}}
            @if($stepIndex === 1)
                @foreach($properties as $propIndex => $prop)
                <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; background-color: #f9f9f9;">
                    <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">
                        Number of floors for: {{ $prop['name'] ?: 'Property ' . ($propIndex + 1) }}
                    </label>
                        <input 
                            type="number" 
                            wire:model="floors.{{ $propIndex }}"
                            placeholder="Total Floors" 
                            style="display: block; width: 100%;"
                        >
                </div>
                @endforeach
            @endif

            {{-- Step 1.5: Floor Deets for every property --}}
@if($stepIndex === 2)
    @foreach($properties as $propIndex => $prop)
        <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; background-color: #f9f9f9;">
            <h3>{{ $prop['name'] ?: 'Property ' . ($propIndex + 1) }}</h3>

            {{-- Floor specs --}}
            @foreach($prop as $key => $details)
@if(\Illuminate\Support\Str::startsWith($key, 'floor_specs_floor') && is_array($details))
    @if(array_key_exists('bottom', $details) && array_key_exists('top', $details))
        <div style="margin-bottom: 1rem; padding-left: 1rem; border-left: 3px solid #ddd;">
            <strong>
                Floor Range for Floor: {{ (int) str_replace('floor_specs_floor', '', $key) }}
            </strong>

            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                <input 
                    type="number" 
                    wire:model="properties.{{ $propIndex }}.{{ $key }}.bottom" 
                    placeholder="Start Room"
                    style="flex: 1;"
                >
                <input 
                    type="number" 
                    wire:model="properties.{{ $propIndex }}.{{ $key }}.top" 
                    placeholder="End Room"
                    style="flex: 1;"
                >
            </div>
        </div>
    @endif
@endif

            @endforeach

            {{-- Increment select belongs here, inside the property block --}}
            <div>
                <strong>Incrementation Ex: 101->102 or 100->110</strong>
                <select 
                    wire:model="properties.{{ $propIndex }}.increment"
                    style="flex: 1;"
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

        {{-- Navigation --}}
        <button type="submit">
            {{ ($stepIndex < $totalSteps - 1) ? 'Next' : 'Submit' }}
        </button>

    </form>
</div>
