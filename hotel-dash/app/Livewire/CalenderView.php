<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\AuxPropertyLog;
use Livewire\Attributes\On;

class CalenderView extends Component
{
    public $currentMonth;
    public $currentYear;
    public $targetType;
    public $days = [];
    public $selectedDate = null;
    public $logs = [];

    public function mount($targetType)
    {
        $this->targetType = strtolower($targetType);
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->generateDays();
    }

    public function generateDays()
    {
        $start = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $start->daysInMonth;

        $this->days = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->currentYear, $this->currentMonth, $day)->toDateString();
            $hasLogs = AuxPropertyLog::whereDate('created_at', $date)->exists();

            $this->days[] = [
                'day' => $day,
                'date' => $date,
                'hasLogs' => $hasLogs,
            ];
        }
    }

    public function selectDay($date)
    {
        $this->selectedDate = $date;
        $this->logs = AuxPropertyLog::whereDate('created_at', $date)->get();
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
