<?php

namespace App\Filament\Admin\Widgets;

use App\Models\CalendarEvent;
use Filament\Widgets\Widget;
use Carbon\Carbon;

class LawFirmCalendarWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.professional-calendar';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public string $currentView = 'month';
    public Carbon $currentDate;

    public function mount(): void
    {
        $this->currentDate = request()->get('date') 
            ? Carbon::parse(request()->get('date')) 
            : Carbon::now();
        $this->currentView = request()->get('view', 'month');
    }

    protected function getViewData(): array
    {
        // Get events for current month
        $startDate = $this->currentDate->copy()->startOfMonth()->startOfWeek();
        $endDate = $this->currentDate->copy()->endOfMonth()->endOfWeek();
        
        $events = CalendarEvent::whereBetween('start_datetime', [$startDate, $endDate])
            ->with(['user:id,name', 'case:id,case_number,case_title', 'client:id,first_name,last_name'])
            ->orderBy('start_datetime')
            ->get();
            
        // Generate calendar data for month view
        $calendarData = $this->generateMonthGrid($this->currentDate, $events);
        
        return [
            'events' => $events,
            'currentDate' => $this->currentDate,
            'currentView' => $this->currentView,
            'calendarData' => $calendarData,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }
    
    private function generateMonthGrid(Carbon $date, $events)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek();
        $endOfCalendar = $endOfMonth->copy()->endOfWeek();
        
        $weeks = [];
        $currentDate = $startOfCalendar->copy();
        
        while ($currentDate <= $endOfCalendar) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dayEvents = $events->filter(function ($event) use ($currentDate) {
                    return $event->start_datetime->format('Y-m-d') === $currentDate->format('Y-m-d');
                });
                
                $week[] = [
                    'date' => $currentDate->copy(),
                    'isCurrentMonth' => $currentDate->month === $date->month,
                    'isToday' => $currentDate->isToday(),
                    'events' => $dayEvents
                ];
                
                $currentDate->addDay();
            }
            $weeks[] = $week;
        }
        
        return $weeks;
    }
}