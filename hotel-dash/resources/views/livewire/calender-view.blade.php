<div class="p-6">
    <h2 class="text-2xl font-bold mb-4 capitalize">{{ $targetType }} Log Calendar</h2>

    <div class="flex justify-between mb-3">
        <button wire:click="previousMonth" class="px-3 py-1 bg-gray-200 rounded">&lt; Prev</button>
        <div class="font-semibold">
            {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
        </div>
        <button wire:click="nextMonth" class="px-3 py-1 bg-gray-200 rounded">Next &gt;</button>
    </div>

    <div class="grid grid-cols-7 gap-1 text-center font-semibold bg-gray-100 p-2 rounded">
        @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
            <div>{{ $day }}</div>
        @endforeach
    </div>

    @php
        $firstDayOfMonth = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->dayOfWeek;
    @endphp

    <div class="grid grid-cols-7 gap-1 mt-2">
        {{-- Empty cells before first day --}}
        @for ($i = 0; $i < $firstDayOfMonth; $i++)
            <div></div>
        @endfor

        {{-- Actual days --}}
        @foreach ($days as $day)
            <div 
                class="border rounded p-2 hover:bg-blue-100 cursor-pointer {{ $day['hasLogs'] ? 'bg-blue-50 border-blue-300' : '' }}"
                wire:click="selectDay('{{ $day['date'] }}')"
            >
                <div class="font-semibold">{{ $day['day'] }}</div>
                @if ($day['hasLogs'])
                    <div class="text-sm text-blue-600">●</div>
                @endif
            </div>
        @endforeach
    </div>

    @if($selectedDate)
        <div class="mt-6 border-t pt-4">
            <h3 class="text-xl font-semibold">Logs for {{ $selectedDate }}</h3>

            @if($logs->count())
                <ul class="mt-2">
                    @foreach($logs as $log)
                        <li class="border p-2 rounded mb-1 bg-gray-50">
                            <pre class="text-sm">{{ json_encode($log->aux_log, JSON_PRETTY_PRINT) }}</pre>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 mt-2">No logs found for this date.</p>
            @endif
        </div>
    @endif
</div>
