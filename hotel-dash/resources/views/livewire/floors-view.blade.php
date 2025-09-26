<div>
    <section class="panel" aria-labelledby="rooms-title">
        <div class="panel-header flex justify-between">
            <h2 id="rooms-title" class="panel-title">Rooms</h2>
            <div>
                <button wire:click="sortBy('name')">Sort by Name</button>
                <button wire:click="sortBy('id')">Sort by ID</button>
            </div>
        </div>

        <div class="panel-body">
            @foreach($floors as $floor)
                <details class="floor" id="floor-{{ $floor['id'] }}" {{ $loop->first ? 'open' : '' }}>
                    <summary class="floor-header">
                        <div class="floor-meta">
                            <span class="floor-name">{{ $floor['name'] ?? 'Unnamed' }}</span>
                            <span class="floor-sub">{{ $floor['total_rooms'] ?? count($floor['rooms'] ?? []) }} rooms</span>
                        </div>
                    </summary>

                    <div class="rooms">
                        @foreach($floor['rooms'] ?? [] as $room)
                            <a class="room {{ $room['room_status'] ?? '' }}" href="#">
                                {{ $room['room'] }}
                            </a>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </div>
    </section>
</div>
