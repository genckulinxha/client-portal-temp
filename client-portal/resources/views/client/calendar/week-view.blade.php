<!-- Week View -->
<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <!-- Days of Week Header -->
    <div class="grid grid-cols-7 bg-gray-50 border-b">
        @foreach($calendarData as $day)
            <div class="p-4 text-center border-r last:border-r-0 {{ $day['isToday'] ? 'bg-blue-50' : '' }}">
                <div class="text-sm font-semibold text-gray-700">{{ $day['date']->format('D') }}</div>
                <div class="text-lg font-bold {{ $day['isToday'] ? 'text-blue-600' : 'text-gray-900' }} mt-1">
                    {{ $day['date']->format('j') }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Time Grid -->
    <div class="grid grid-cols-8 min-h-[600px]">
        <!-- Time Labels -->
        <div class="bg-gray-50 border-r">
            @for($hour = 7; $hour <= 19; $hour++)
                <div class="h-16 border-b flex items-center justify-center text-xs text-gray-600">
                    {{ Carbon\Carbon::createFromTime($hour)->format('g A') }}
                </div>
            @endfor
        </div>

        <!-- Days -->
        @foreach($calendarData as $day)
            <div class="border-r last:border-r-0 relative {{ $day['isToday'] ? 'bg-blue-50' : '' }}">
                @for($hour = 7; $hour <= 19; $hour++)
                    <div class="h-16 border-b"></div>
                @endfor
                
                <!-- Events for this day -->
                @foreach($day['events'] as $event)
                    @php
                        $startHour = $event->start_datetime->hour;
                        $startMinute = $event->start_datetime->minute;
                        $topPosition = (($startHour - 7) * 64) + ($startMinute * 64 / 60);
                        $duration = $event->end_datetime ? $event->start_datetime->diffInMinutes($event->end_datetime) : 60;
                        $height = max(20, ($duration * 64 / 60));
                    @endphp
                    
                    @if($startHour >= 7 && $startHour <= 19)
                        <div class="absolute left-1 right-1 rounded p-1 text-xs overflow-hidden {{ 
                            $event->type === 'court_hearing' ? 'bg-red-200 text-red-800 border-l-2 border-red-500' : 
                            ($event->type === 'deposition' ? 'bg-yellow-200 text-yellow-800 border-l-2 border-yellow-500' : 
                            ($event->type === 'consultation' ? 'bg-green-200 text-green-800 border-l-2 border-green-500' : 
                            ($event->type === 'meeting' ? 'bg-blue-200 text-blue-800 border-l-2 border-blue-500' : 'bg-gray-200 text-gray-800 border-l-2 border-gray-500')))
                        }}" style="top: {{ $topPosition }}px; height: {{ $height }}px;">
                            <div class="font-medium truncate">{{ $event->title }}</div>
                            <div class="text-xs opacity-75">{{ $event->start_datetime->format('g:i A') }}</div>
                            @if($event->location)
                                <div class="text-xs opacity-75 truncate">{{ $event->location }}</div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
</div>