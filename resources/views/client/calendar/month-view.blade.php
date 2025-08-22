<!-- Month View -->
<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <!-- Days of Week Header -->
    <div class="grid grid-cols-7 bg-gray-50 border-b">
        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="p-4 text-center text-sm font-semibold text-gray-700 border-r last:border-r-0">
                {{ $day }}
            </div>
        @endforeach
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7">
        @foreach($calendarData as $week)
            @foreach($week as $day)
                <div class="min-h-[120px] border-r border-b last:border-r-0 p-2 {{ !$day['isCurrentMonth'] ? 'bg-gray-50' : 'bg-white' }} {{ $day['isToday'] ? 'bg-blue-50' : '' }}">
                    <!-- Date Number -->
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium {{ !$day['isCurrentMonth'] ? 'text-gray-400' : ($day['isToday'] ? 'text-blue-600' : 'text-gray-900') }}">
                            {{ $day['date']->format('j') }}
                        </span>
                        @if($day['isToday'])
                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        @endif
                    </div>

                    <!-- Events -->
                    <div class="space-y-1">
                        @foreach($day['events']->take(3) as $event)
                            <div class="text-xs p-1 rounded truncate {{ 
                                $event->type === 'court_hearing' ? 'bg-red-100 text-red-800' : 
                                ($event->type === 'deposition' ? 'bg-yellow-100 text-yellow-800' : 
                                ($event->type === 'consultation' ? 'bg-green-100 text-green-800' : 
                                ($event->type === 'meeting' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')))
                            }}" title="{{ $event->title }} - {{ $event->start_datetime->format('g:i A') }}">
                                <div class="flex items-center space-x-1">
                                    <div class="w-1.5 h-1.5 rounded-full {{ 
                                        $event->type === 'court_hearing' ? 'bg-red-500' : 
                                        ($event->type === 'deposition' ? 'bg-yellow-500' : 
                                        ($event->type === 'consultation' ? 'bg-green-500' : 
                                        ($event->type === 'meeting' ? 'bg-blue-500' : 'bg-gray-500')))
                                    }}"></div>
                                    <span class="truncate">{{ $event->start_datetime->format('g:i A') }} {{ $event->title }}</span>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($day['events']->count() > 3)
                            <div class="text-xs text-gray-500 pl-1">
                                +{{ $day['events']->count() - 3 }} more
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>