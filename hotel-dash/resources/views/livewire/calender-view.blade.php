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
                class="rounded p-2 hover:bg-blue-100 cursor-pointer 
                    @if ($day['hasLogs']) border border-green-300 bg-green-50 @else border border-red-300 bg-red-50 @endif"
                wire:click="selectDay('{{ $day['date'] }}')"
            >
                <div class="font-semibold flex items-center justify-center space-x-1">
                    <span>{{ $day['day'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Logs and input area --}}
    <div class="mt-6 p-6 border rounded bg-gray-50">
        @if($selectedDate)
            <h3 class="text-xl font-semibold mb-3">Logs for {{ $selectedDate }}</h3>

            {{-- Existing Logs Display --}}
            @if($logs && $logs->count())
                <div class="space-y-2">
                    @foreach($logs as $log)
                        <div class="message border p-3 rounded bg-white flex justify-between items-start">
                            <div>
                                <pre class="text-sm">{{ json_encode($log->aux_log, JSON_PRETTY_PRINT) }}</pre>
                                <div class="text-xs text-gray-400 mt-1">
                                    Logged at: {{ \Carbon\Carbon::parse($log->created_at)->format('m-d-Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 mt-2">No logs found for this date.</p>
            @endif

            {{-- Pool Log Input --}}
            <div class="mt-4">
                <h4 class="font-semibold mb-2">Add Pool Log</h4>
                <div class="message border rounded p-4 bg-blue-50">
                    <form wire:submit.prevent="savePoolLog" class="space-y-2">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium">pH</label>
                                <input type="number" step="0.01" min="0" wire:model.defer="poolLog.ph" class="w-full border rounded px-2 py-1">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Free Chlorine</label>
                                <input type="number" step="0.01" min="0" wire:model.defer="poolLog.free_chlorine" class="w-full border rounded px-2 py-1">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Combined Chlorine</label>
                                <input type="number" step="0.01" min="0" wire:model.defer="poolLog.combined_chlorine" class="w-full border rounded px-2 py-1">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Calcium</label>
                                <input type="number" step="0.01" min="0" wire:model.defer="poolLog.calcium" class="w-full border rounded px-2 py-1">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium">CYA (Stabilizer)</label>
                                <input type="number" step="0.01" min="0" wire:model.defer="poolLog.cya" class="w-full border rounded px-2 py-1">
                            </div>
                        </div>
                        <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Save Log
                        </button>
                    </form>
                </div>
            </div>

        @else
            <p class="text-gray-500 mb-2">Select a date to view or add logs.</p>
        @endif
    </div>

</div>
