<!-- Day View -->
<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <!-- Day Header -->
    <div class="bg-gray-50 border-b p-4">
        <div class="text-center">
            <div class="text-sm font-semibold text-gray-700">{{ $currentDate->format('l') }}</div>
            <div class="text-2xl font-bold {{ $currentDate->isToday() ? 'text-blue-600' : 'text-gray-900' }} mt-1">
                {{ $currentDate->format('j') }}
            </div>
            <div class="text-sm text-gray-600">{{ $currentDate->format('F Y') }}</div>
        </div>
    </div>

    <!-- Time Grid -->
    <div class="grid grid-cols-2 min-h-[600px]">
        <!-- Time Labels -->
        <div class="bg-gray-50 border-r">
            @for($hour = 7; $hour <= 19; $hour++)
                <div class="h-16 border-b flex items-center justify-center text-sm text-gray-600">
                    {{ Carbon\Carbon::createFromTime($hour)->format('g:00 A') }}
                </div>
            @endfor
        </div>

        <!-- Day Column -->
        <div class="relative {{ $currentDate->isToday() ? 'bg-blue-50' : '' }}">
            @for($hour = 7; $hour <= 19; $hour++)
                <div class="h-16 border-b"></div>
            @endfor
            
            <!-- Events for this day -->
            @foreach($events as $event)
                @php
                    $startHour = $event->start_datetime->hour;
                    $startMinute = $event->start_datetime->minute;
                    $topPosition = (($startHour - 7) * 64) + ($startMinute * 64 / 60);
                    $duration = $event->end_datetime ? $event->start_datetime->diffInMinutes($event->end_datetime) : 60;
                    $height = max(40, ($duration * 64 / 60));
                @endphp
                
                @if($startHour >= 7 && $startHour <= 19)
                    <div class="absolute left-2 right-2 rounded-lg p-3 text-sm shadow-sm {{ 
                        $event->type === 'court_hearing' ? 'bg-red-100 text-red-800 border border-red-200' : 
                        ($event->type === 'deposition' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                        ($event->type === 'consultation' ? 'bg-green-100 text-green-800 border border-green-200' : 
                        ($event->type === 'meeting' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-gray-100 text-gray-800 border border-gray-200')))
                    }}" style="top: {{ $topPosition }}px; height: {{ $height }}px;">
                        <div class="font-semibold">{{ $event->title }}</div>
                        <div class="text-xs mt-1 opacity-75">
                            {{ $event->start_datetime->format('g:i A') }}
                            @if($event->end_datetime)
                                - {{ $event->end_datetime->format('g:i A') }}
                            @endif
                        </div>
                        @if($event->location)
                            <div class="text-xs mt-1 opacity-75 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $event->location }}
                            </div>
                        @endif
                        @if($event->user)
                            <div class="text-xs mt-1 opacity-75 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ $event->user->name }}
                            </div>
                        @endif
                        @if($event->case)
                            <div class="text-xs mt-1 opacity-75">Case: {{ $event->case->case_number }}</div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<!-- Events outside business hours -->
@if($events->filter(fn($e) => $e->start_datetime->hour < 7 || $e->start_datetime->hour > 19)->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-sm border">
        <div class="p-4 border-b">
            <h3 class="font-semibold text-gray-900">Other Events</h3>
        </div>
        <div class="p-4 space-y-3">
            @foreach($events->filter(fn($e) => $e->start_datetime->hour < 7 || $e->start_datetime->hour > 19) as $event)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full {{ $event->type === 'court_hearing' ? 'bg-red-500' : ($event->type === 'consultation' ? 'bg-green-500' : 'bg-blue-500') }}"></div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $event->title }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $event->start_datetime->format('g:i A') }}
                                @if($event->location)
                                    • {{ $event->location }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($event->user)
                            <p class="text-sm text-gray-600">{{ $event->user->name }}</p>
                        @endif
                        @if($event->case)
                            <p class="text-xs text-gray-500">{{ $event->case->case_number }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif