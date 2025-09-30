@push('styles')
    @vite(['resources/css/floorview_style.css'])
@endpush

<div>
    <section class="panel" aria-labelledby="rooms-title">
        <div class="panel-header flex justify-between">
            <h2 id="rooms-title" class="panel-title">Properties</h2>
            <div class="flex gap-2">
            <input type="text" wire:model.live="search" placeholder="Search..." />
            </div>
        </div>
        <div class="panel-body">
            @forelse($floors as $property)
                <div class="property-block">
                    <h3 class="property-title">{{ $property['property_name'] ?? 'Unnamed Property' }}</h3>
                    @forelse($property['floors'] as $floor)
                        <details class="floor" id="floor-{{ $floor['floor_num'] }}" {{ $loop->first ? 'open' : '' }}>
                            <summary class="floor-header">
                                <div class="floor-meta">
                                    <span class="floor-name">
                                        Floor {{ $floor['floor_num'] ?? $floor['floor_number'] }}
                                    </span>
                                    <span class="floor-sub">
                                        {{ $floor['total_rooms'] ?? count($floor['rooms'] ?? []) }} rooms
                                    </span>
                                </div>
                            </summary>
                            <div class="rooms">
                                @foreach($floor['rooms'] ?? [] as $room)
                                    <a class="room {{ $room['room_status'] ?? '' }}"
                                       href="{{ route('room.board', [
                                           'property' => $property['property_id'],
                                           'floor'    => $floor['id'],
                                           'room'     => $room['id'],
                                       ]) }}">
                                        Room {{ $room['room'] }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
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
