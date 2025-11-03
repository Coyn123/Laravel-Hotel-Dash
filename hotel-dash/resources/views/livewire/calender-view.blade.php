<div class="panel calendar-container">

    <h2 class="panel-title mb-4 capitalize">{{ $targetType }} Log Calendar</h2>

    <div class="calendar-nav">
        <button wire:click="previousMonth" class="btn btn-secondary text-sm px-3 py-1">&lt; Prev</button>
        <div class="font-semibold">
            {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
        </div>
        <button wire:click="nextMonth" class="btn btn-secondary text-sm px-3 py-1">Next &gt;</button>
    </div>

    <div class="calendar-grid-header">
        @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
        <div>{{ $day }}</div>
        @endforeach
    </div>

    @php
    $firstDayOfMonth = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->dayOfWeek;
    @endphp

    <div class="calendar-grid-days">
        {{-- Empty cells before first day --}}
        @for ($i = 0; $i < $firstDayOfMonth; $i++)
            <div>
    </div>
    @endfor

    {{-- Actual days --}}
    @foreach ($days as $day)
    <div
        class="calendar-day {{ $day['hasLogs'] ? 'has-logs' : 'no-logs' }}"
        wire:click="selectDay('{{ $day['date'] }}')">
        <div class="font-semibold flex items-center justify-center">
            {{ $day['day'] }}
        </div>
    </div>
    @endforeach
</div>

<div class="calendar-log-section">
    @if($selectedDate)
    <h3>Logs for {{ $selectedDate }}</h3>

    @if($logs && $logs->count())
    @foreach($logs as $log)
    <div class="calendar-log-entry">
        <div>
            @if(is_array($log->aux_log))
            @php
            // Same mapping as input form
            $fieldLabels = [
            'ph' => 'pH',
            'fChlor' => 'Free Chlorine',
            'cChlor' => 'Used Chlorine',
            'calc' => 'Calcium',
            'Alk' => 'Alkalinity',
            'cya' => '(CYA) Acid'
            ];
            @endphp

            <table class="log-json-table">
                <tbody>
                    @foreach($fieldLabels as $key => $label)
                    @php
                    $value = $log->aux_log[$key] ?? '—';
                    @endphp
                    <tr>
                        <td class="log-key">{{ $label }}</td>
                        <td class="log-value">
                            @if(is_array($value))
                            {{ json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}
                            @else
                            {{ $value !== null && $value !== '' ? $value : '—' }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-gray-400 text-sm italic">Invalid log data</p>
            @endif


            <div class="text-xs mt-1">
                Logged at: {{ \Carbon\Carbon::parse($log->created_at)->format('m-d-Y H:i') }}
            </div>
        </div>
    </div>
    @endforeach
    @else
    <p class="mt-2">No logs found for this date.</p>
    @endif

    <div class="calendar-input-form">
        <h4 class="font-semibold mb-2">Add Pool Log</h4>
        <form wire:submit.prevent="savePoolLog">
            <div class="grid grid-cols-2 gap-3">
                @foreach ([
                'ph' => 'pH',
                'fChlor' => 'Free Chlorine',
                'cChlor' => 'Combined Chlorine',
                'calc' => 'Calcium',
                'Alk' => 'Alkalinity',
                'cya' => 'CYA (Stabilizer)'
                ] as $key => $label)
                <div>
                    <label class="block text-sm font-medium">{{ $label }}</label>
                    <input type="number" step="0.01" min="0" wire:model.defer="poolLog.{{ $key }}">
                </div>
                @endforeach
            </div>

            <button type="submit">
                Save Log
            </button>
        </form>
    </div>
    @else
    <p class="text-gray-500 mb-2">Select a date to view or add logs.</p>
    @endif
</div>

</div>