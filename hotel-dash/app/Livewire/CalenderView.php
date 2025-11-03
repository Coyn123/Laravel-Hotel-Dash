<?php

namespace App\Livewire;

use App\Services\DashboardConfig;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\AuxPropertyLog;

class CalenderView extends Component
{
    public $currentMonth;
    public $currentYear;
    public $targetType;
    public $days = [];
    public $selectedDate = null;
    public $logs;
    public $config;
    public $auxID;
    public $poolLog = [
            'ph' => null,
            'fChlor' => null,
            'cChlor' => null,
            'calc' => null,
            'Alk' => null,
            'cya' => null,
        ];

    public function mount($targetType)
    {
        $this->targetType = strtolower($targetType);
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->logs = collect();

        $this->config = DashboardConfig::get();

        foreach ($this->config['properties'] as $property) {
            foreach ($property['aux_properties'] as $aux) {
                if (strtolower($aux['aux_type']) === $this->targetType) {
                    $this->auxID = $aux['id'];
                    break 2; // found the first matching aux property
                }
            }
        }
        $this->generateDays();

    }

public function savePoolLog()
{
    $auxID = $this->auxID;
    $this->validate([
        'poolLog.ph' => 'nullable|numeric|min:0',
        'poolLog.fChlor' => 'nullable|numeric|min:0',
        'poolLog.cChlor' => 'nullable|numeric|min:0',
        'poolLog.calc' => 'nullable|numeric|min:0',
        'poolLog.alk' => 'nullable|numeric|min:0',
        'poolLog.cya' => 'nullable|numeric|min:0',
    ]);

    if (!$auxID) {
        $this->addError('poolLog', 'Aux property ID not found for this target type.');
        return;
    }

    AuxPropertyLog::create([
        'aux_id' => $auxID,
        'aux_log' => $this->poolLog,
        'log_date' => Carbon::parse($this->selectedDate)->toDateString(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->poolLog = [
        'ph' => null,
        'fChlor' => null,
        'cChlor' => null,
        'calc' => null,
        'Alk' => null,
        'cya' => null,
    ];

    $this->selectDay($this->selectedDate);
    $this->generateDays();
}


    public function generateDays()
    {
        $start = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $start->daysInMonth;

        // Pull all log dates for this aux in one query
        $datesWithLogs = AuxPropertyLog::query()
            ->where('aux_id', $this->auxID)
            ->whereMonth('log_date', $this->currentMonth)
            ->whereYear('log_date', $this->currentYear)
            ->pluck('log_date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->flip();

        $this->days = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->currentYear, $this->currentMonth, $day)->toDateString();

            $this->days[] = [
                'day' => $day,
                'date' => $date,
                'hasLogs' => isset($datesWithLogs[$date]),
            ];
        }
    }


    public function selectDay($date)
    {
        $auxID = $this->auxID;

        $this->selectedDate = $date;
        $this->logs = AuxPropertyLog::where('aux_id', $auxID)
                        ->whereDate('log_date', $date)
                        ->get();

        // Ensure $logs is always a Collection
        if (!$this->logs) {
            $this->logs = collect();
        }
    }




    public function previousMonth()
    {
        $prev = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $prev->month;
        $this->currentYear = $prev->year;
        $this->generateDays();
    }

    public function nextMonth()
    {
        $next = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $next->month;
        $this->currentYear = $next->year;
        $this->generateDays();
    }

    public function render()
    {
        return view('livewire.calender-view');
    }
}
