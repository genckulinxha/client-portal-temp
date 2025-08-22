<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->client;
        $currentDate = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::now();
        $view = $request->get('view', 'month'); // month, week, day
        
        // Get events for the client based on view
        switch ($view) {
            case 'week':
                $startDate = $currentDate->copy()->startOfWeek();
                $endDate = $currentDate->copy()->endOfWeek();
                break;
            case 'day':
                $startDate = $currentDate->copy()->startOfDay();
                $endDate = $currentDate->copy()->endOfDay();
                break;
            default: // month
                $startDate = $currentDate->copy()->startOfMonth()->startOfWeek();
                $endDate = $currentDate->copy()->endOfMonth()->endOfWeek();
                break;
        }
        
        $events = CalendarEvent::where('client_id', $client->id)
            ->whereBetween('start_datetime', [$startDate, $endDate])
            ->with(['user:id,name', 'case:id,case_number'])
            ->orderBy('start_datetime')
            ->get();
            
        // Generate calendar grid for month view
        $calendarData = [];
        if ($view === 'month') {
            $calendarData = $this->generateMonthGrid($currentDate, $events);
        } elseif ($view === 'week') {
            $calendarData = $this->generateWeekGrid($currentDate, $events);
        }
        
        return view('client.calendar.index', compact(
            'client',
            'events',
            'currentDate',
            'view',
            'calendarData',
            'startDate',
            'endDate'
        ));
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
    
    private function generateWeekGrid(Carbon $date, $events)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $days = [];
        
        for ($i = 0; $i < 7; $i++) {
            $currentDay = $startOfWeek->copy()->addDays($i);
            $dayEvents = $events->filter(function ($event) use ($currentDay) {
                return $event->start_datetime->format('Y-m-d') === $currentDay->format('Y-m-d');
            });
            
            $days[] = [
                'date' => $currentDay,
                'isToday' => $currentDay->isToday(),
                'events' => $dayEvents
            ];
        }
        
        return $days;
    }
}