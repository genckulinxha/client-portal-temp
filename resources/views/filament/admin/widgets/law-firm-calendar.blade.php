<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Calendar - {{ $currentDate->format('F Y') }}
            </h3>
            <div class="flex items-center gap-2">
                <a href="{{ request()->fullUrlWithQuery(['date' => $currentDate->copy()->subMonth()->format('Y-m-d')]) }}" 
                   class="inline-flex items-center justify-center w-8 h-8 text-gray-500 transition-colors rounded-lg hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['date' => now()->format('Y-m-d')]) }}" 
                   class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    Today
                </a>
                <a href="{{ request()->fullUrlWithQuery(['date' => $currentDate->copy()->addMonth()->format('Y-m-d')]) }}" 
                   class="inline-flex items-center justify-center w-8 h-8 text-gray-500 transition-colors rounded-lg hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Days of Week Header -->
        <div class="grid grid-cols-7 gap-px mb-2 text-center bg-gray-200 rounded-lg dark:bg-gray-700">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div class="py-3 text-xs font-semibold text-gray-700 bg-gray-50 first:rounded-l-lg last:rounded-r-lg dark:bg-gray-800 dark:text-gray-300">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600">
            @foreach($calendarData as $weekIndex => $week)
                @foreach($week as $dayIndex => $day)
                    <div class="relative h-[120px] p-3 bg-white flex flex-col
                        {{ !$day['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-800' : 'dark:bg-gray-900' }} 
                        {{ $day['isToday'] ? 'ring-2 ring-blue-500 ring-inset' : '' }}
                        {{ $weekIndex === 0 && $dayIndex === 0 ? 'rounded-tl-lg' : '' }}
                        {{ $weekIndex === 0 && $dayIndex === 6 ? 'rounded-tr-lg' : '' }}
                        {{ $weekIndex === count($calendarData) - 1 && $dayIndex === 0 ? 'rounded-bl-lg' : '' }}
                        {{ $weekIndex === count($calendarData) - 1 && $dayIndex === 6 ? 'rounded-br-lg' : '' }}">
                        
                        <!-- Date Number -->
                        <div class="mb-2 flex-shrink-0">
                            <time datetime="{{ $day['date']->format('Y-m-d') }}" 
                                  class="text-sm font-medium {{ !$day['isCurrentMonth'] ? 'text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-gray-100' }} {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                {{ $day['date']->format('j') }}
                            </time>
                        </div>
                        
                        <!-- Events -->
                        <div class="flex-1 overflow-hidden">
                            @if($day['events']->count() > 0)
                                <div class="space-y-1">
                                    @foreach($day['events']->take(3) as $event)
                                        <div class="p-2 text-xs rounded cursor-pointer transition-colors h-12 flex items-center {{ 
                                            $event->type === 'court_hearing' ? 'bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300' : 
                                            ($event->type === 'deposition' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300' : 
                                            ($event->type === 'consultation' ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300' : 
                                            ($event->type === 'meeting' ? 'bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300' : 
                                            ($event->type === 'deadline' ? 'bg-orange-100 text-orange-800 hover:bg-orange-200 dark:bg-orange-900/30 dark:text-orange-300' : 'bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'))))
                                        }}" 
                                        onclick="window.location.href='{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('edit', ['record' => $event]) }}'">
                                            <div class="truncate w-full">
                                                <div class="font-medium">{{ $event->start_datetime->format('g:i A') }}</div>
                                                <div class="text-xs opacity-80">{{ Str::limit($event->title, 18) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($day['events']->count() > 3)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 pl-1">
                                            +{{ $day['events']->count() - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Event
                </a>
                <a href="{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    List View
                </a>
            </div>
            
            <!-- Legend -->
            <div class="flex items-center space-x-4 text-xs">
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-red-500 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Court</span>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-green-500 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Consultation</span>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-blue-500 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Meeting</span>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-3 h-3 bg-yellow-500 rounded"></div>
                    <span class="text-gray-600 dark:text-gray-400">Deposition</span>
                </div>
            </div>
        </div>

        <!-- Today's Events Summary -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Today's Events</h4>
            @php
                $todayEvents = $events->filter(fn($event) => $event->start_datetime->isToday());
            @endphp
            @if($todayEvents->count() > 0)
                <div class="space-y-2">
                    @foreach($todayEvents->take(5) as $event)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors dark:bg-gray-800 dark:hover:bg-gray-700"
                             onclick="window.location.href='{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('edit', ['record' => $event]) }}'">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 rounded-full {{ 
                                    $event->type === 'court_hearing' ? 'bg-red-500' : 
                                    ($event->type === 'consultation' ? 'bg-green-500' : 
                                    ($event->type === 'meeting' ? 'bg-blue-500' : 
                                    ($event->type === 'deposition' ? 'bg-yellow-500' : 'bg-gray-500')))
                                }}"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $event->start_datetime->format('g:i A') }}
                                        @if($event->client)
                                            • {{ $event->client->first_name }} {{ $event->client->last_name }}
                                        @endif
                                        @if($event->case)
                                            • {{ $event->case->case_number }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                $event->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 
                                ($event->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 
                                ($event->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))
                            }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No events scheduled for today.</p>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>