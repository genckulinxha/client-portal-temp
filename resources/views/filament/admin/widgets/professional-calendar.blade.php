<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- Header with Navigation -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $currentDate->format('F Y') }}
                    </h2>
                    <div class="flex items-center space-x-2">
                        <a href="{{ request()->fullUrlWithQuery(['date' => $currentDate->copy()->subMonth()->format('Y-m-d')]) }}" 
                           class="inline-flex items-center justify-center w-9 h-9 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['date' => now()->format('Y-m-d')]) }}" 
                           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            Today
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['date' => $currentDate->copy()->addMonth()->format('Y-m-d')]) }}" 
                           class="inline-flex items-center justify-center w-9 h-9 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- View Options and Actions -->
                <div class="flex items-center space-x-3">
                    <a href="{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Event
                    </a>
                    <a href="{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        List View
                    </a>
                </div>
            </div>

            <!-- Days of Week Header -->
            <div class="grid grid-cols-7 gap-px bg-gray-300 rounded-lg overflow-hidden dark:bg-gray-700">
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                    <div class="bg-gray-100 px-4 py-3 text-center text-sm font-bold text-gray-800 dark:bg-gray-900 dark:text-gray-100 border-b-2 border-gray-300 dark:border-gray-500">
                        <span class="hidden sm:inline">{{ $day }}</span>
                        <span class="sm:hidden">{{ substr($day, 0, 3) }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-px bg-gray-200 rounded-lg overflow-hidden dark:bg-gray-600">
                @foreach($calendarData as $weekIndex => $week)
                    @foreach($week as $dayIndex => $day)
                        <div class="bg-white w-full h-32 p-2 relative group hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-750 transition-colors border border-gray-300 dark:border-gray-600
                            {{ !$day['isCurrentMonth'] ? 'bg-gray-50 text-gray-400 dark:bg-gray-850 dark:text-gray-500' : 'bg-white dark:bg-gray-800' }}
                            {{ $day['isToday'] ? 'ring-2 ring-blue-500 ring-inset bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/40 dark:hover:bg-blue-900/60 dark:ring-blue-400' : '' }}">
                            
                            <!-- Date Number -->
                            <div class="flex items-center justify-between mb-1">
                                <time datetime="{{ $day['date']->format('Y-m-d') }}" 
                                      class="text-sm font-bold {{ !$day['isCurrentMonth'] ? 'text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-gray-100' }} {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-300 font-extrabold text-base' : '' }}">
                                    {{ $day['date']->format('j') }}
                                </time>
                                
                                <!-- Add Event Button (shows on hover) -->
                                <button onclick="window.location.href='{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('create', ['start_datetime' => $day['date']->format('Y-m-d')]) }}'" 
                                        class="opacity-0 group-hover:opacity-100 w-5 h-5 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded transition-all dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Events -->
                            <div class="space-y-1 overflow-hidden">
                                @foreach($day['events']->take(2) as $event)
                                    <div class="group/event cursor-pointer p-1 rounded text-xs transition-all border-l-2 {{ 
                                        $event->type === 'court_hearing' ? 'bg-red-100 text-red-800 hover:bg-red-200 border-red-500 dark:bg-red-900/50 dark:text-red-200 dark:hover:bg-red-900/70 dark:border-red-400' : 
                                        ($event->type === 'deposition' ? 'bg-amber-100 text-amber-800 hover:bg-amber-200 border-amber-500 dark:bg-amber-900/50 dark:text-amber-200 dark:hover:bg-amber-900/70 dark:border-amber-400' : 
                                        ($event->type === 'consultation' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200 border-emerald-500 dark:bg-emerald-900/50 dark:text-emerald-200 dark:hover:bg-emerald-900/70 dark:border-emerald-400' : 
                                        ($event->type === 'meeting' ? 'bg-blue-100 text-blue-800 hover:bg-blue-200 border-blue-500 dark:bg-blue-900/50 dark:text-blue-200 dark:hover:bg-blue-900/70 dark:border-blue-400' : 
                                        ($event->type === 'deadline' ? 'bg-orange-100 text-orange-800 hover:bg-orange-200 border-orange-500 dark:bg-orange-900/50 dark:text-orange-200 dark:hover:bg-orange-900/70 dark:border-orange-400' : 'bg-gray-100 text-gray-800 hover:bg-gray-200 border-gray-500 dark:bg-gray-700/50 dark:text-gray-200 dark:hover:bg-gray-700/70 dark:border-gray-400'))))
                                    }}" 
                                    onclick="window.location.href='{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('edit', ['record' => $event]) }}'"
                                    title="{{ $event->title }} - {{ $event->start_datetime->format('g:i A') }}{{ $event->description ? ' | ' . $event->description : '' }}">
                                        
                                        <div class="truncate font-medium">{{ $event->start_datetime->format('g:i A') }}</div>
                                        <div class="truncate text-xs opacity-90">{{ Str::limit($event->title, 15) }}</div>
                                    </div>
                                @endforeach
                                
                                @if($day['events']->count() > 2)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 px-1">
                                        +{{ $day['events']->count() - 2 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <!-- Legend and Today's Events -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                <!-- Event Type Legend -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Event Types
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-4 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors">
                            <div class="w-4 h-4 bg-red-100 border-l-4 border-red-500 rounded-sm dark:bg-red-800/60 dark:border-red-400"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-900">Court Hearing</span>
                        </div>
                        <div class="flex items-center space-x-4 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors">
                            <div class="w-4 h-4 bg-amber-100 border-l-4 border-amber-500 rounded-sm dark:bg-amber-800/60 dark:border-amber-400"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-900">Deposition</span>
                        </div>
                        <div class="flex items-center space-x-4 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors">
                            <div class="w-4 h-4 bg-emerald-100 border-l-4 border-emerald-500 rounded-sm dark:bg-emerald-800/60 dark:border-emerald-400"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-900">Consultation</span>
                        </div>
                        <div class="flex items-center space-x-4 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors">
                            <div class="w-4 h-4 bg-blue-100 border-l-4 border-blue-500 rounded-sm dark:bg-blue-800/60 dark:border-blue-400"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-900">Meeting</span>
                        </div>
                        <div class="flex items-center space-x-4 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors">
                            <div class="w-4 h-4 bg-orange-100 border-l-4 border-orange-500 rounded-sm dark:bg-orange-800/60 dark:border-orange-400"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-900">Deadline</span>
                        </div>
                    </div>
                </div>

                <!-- Today's Events Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Today's Events
                    </h4>
                    @php
                        $todaysEvents = $events->filter(fn($event) => $event->start_datetime->isToday())->take(5);
                    @endphp
                    @if($todaysEvents->count() > 0)
                        <div class="space-y-3">
                            @foreach($todaysEvents as $event)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-750 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors border border-gray-100 dark:border-gray-600"
                                     onclick="window.location.href='{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('edit', ['record' => $event]) }}'">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 rounded-full {{ 
                                            $event->type === 'court_hearing' ? 'bg-red-500' : 
                                            ($event->type === 'consultation' ? 'bg-emerald-500' : 
                                            ($event->type === 'meeting' ? 'bg-blue-500' : 
                                            ($event->type === 'deposition' ? 'bg-amber-500' : 'bg-gray-500')))
                                        }}"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->title }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $event->start_datetime->format('g:i A') }}
                                                @if($event->client)
                                                    • {{ $event->client->first_name }} {{ $event->client->last_name }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ 
                                        $event->status === 'scheduled' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 
                                        ($event->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 
                                        ($event->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))
                                    }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <svg class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 12V11a2 2 0 012-2h4a2 2 0 012 2v8M5 11a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2v-8z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No events today</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your schedule is clear for today.</p>
                            <div class="mt-3">
                                <a href="{{ \App\Filament\Admin\Resources\CalendarEventResource::getUrl('create') }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Add Event
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>