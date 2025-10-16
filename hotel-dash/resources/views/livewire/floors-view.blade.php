<div>
    <section class="panel" aria-labelledby="rooms-title">
        <div class="panel-header flex justify-between">
            <h2 id="rooms-title" class="panel-title">Properties</h2>
            <div class="search gap-2">
                <input type="text" wire:model.live="search" placeholder="Search..." />
            </div>
        </div>

        <div class="panel-body">
            @forelse($properties as $property)
                <div class="property-block">
                    <h3 class="property-title">{{ $property['property_name'] ?? 'Unnamed Property' }}</h3>

                    @forelse($property['floors'] as $floor)
                        @php
                            // Composite key: property_id-floor_num
                            $floorKey = $property['property_id'].'-'.$floor['floor_num'];
                        @endphp

                        <div class="floor border rounded mb-2" id="floor-{{ $floorKey }}">
                            <div 
                                class="floor-header cursor-pointer flex justify-between items-center p-2"
                                wire:click="toggleFloor('{{ $floorKey }}')"
                            >
                                <div class="floor-meta">
                                    <span class="floor-name font-semibold">
                                        Floor {{ $floor['floor_num'] ?? $floor['floor_number'] }}
                                    </span>
                                    <span class="floor-sub text-sm ml-2">
                                        {{ $floor['total_rooms'] ?? count($floor['rooms'] ?? []) }} rooms
                                    </span>
                                </div>
                                <svg 
                                    class="h-4 w-4 transform transition-transform duration-200 {{ in_array($floorKey, $openFloors) ? 'rotate-180' : '' }}" 
                                    xmlns="http://www.w3.org/2000/svg" 
                                    fill="none" 
                                    viewBox="0 0 24 24" 
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            @if(in_array($floorKey, $openFloors))
                                <div class="rooms p-2 space-y-1">
                                    @foreach($floor['rooms'] ?? [] as $room)
                                        <a class="room block px-2 py-1 rounded hover:bg-gray-50 {{ $room['room_status'] ?? '' }}"
                                            wire:click.prevent="selectRoom({{ $room['property_id'] }}, {{ $room['floor'] }}, {{ $room['id'] }})"
                                            href="#"
                                        >
                                            {{ $room['room'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">No floors match your search.</p>
                    @endforelse
                </div>
            @empty
                <p class="text-gray-500">No properties match your search.</p>
            @endforelse
        </div>
    </section>
</div>
