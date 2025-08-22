@extends('client.layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Calendar Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
            <h1 class="text-2xl font-bold text-gray-900">Calendar</h1>
            <div class="flex items-center space-x-2">
                <!-- View Toggle -->
                <div class="bg-white rounded-lg shadow-sm border flex">
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'month']) }}" 
                       class="px-3 py-2 text-sm font-medium {{ $view === 'month' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-blue-600' }} rounded-l-lg transition">
                        Month
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'week']) }}" 
                       class="px-3 py-2 text-sm font-medium {{ $view === 'week' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-blue-600' }} transition">
                        Week
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'day']) }}" 
                       class="px-3 py-2 text-sm font-medium {{ $view === 'day' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-blue-600' }} rounded-r-lg transition">
                        Day
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Navigation Controls -->
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <a href="{{ request()->fullUrlWithQuery(['date' => $currentDate->copy()->subMonth()->format('Y-m-d')]) }}" 
                   class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="text-lg font-semibold text-gray-800 min-w-[200px] text-center">
                    {{ $currentDate->format('F Y') }}
                </h2>
                <a href="{{ request()->fullUrlWithQuery(['date' => $currentDate->copy()->addMonth()->format('Y-m-d')]) }}" 
                   class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            <a href="{{ request()->fullUrlWithQuery(['date' => now()->format('Y-m-d')]) }}" 
               class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Today
            </a>
        </div>
    </div>

    <!-- Calendar Views -->
    @if($view === 'month')
        @include('client.calendar.month-view')
    @elseif($view === 'week')
        @include('client.calendar.week-view')
    @else
        @include('client.calendar.day-view')
    @endif

    <!-- Upcoming Events Summary -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Events</h3>
            @if($events->where('start_datetime', '>', now())->count() > 0)
                <div class="space-y-3">
                    @foreach($events->where('start_datetime', '>', now())->take(5) as $event)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full {{ $event->type === 'court_hearing' ? 'bg-red-500' : ($event->type === 'consultation' ? 'bg-green-500' : 'bg-blue-500') }}"></div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $event->title }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $event->start_datetime->format('M j, Y g:i A') }}
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
            @else
                <p class="text-gray-500 text-center py-8">No upcoming events scheduled.</p>
            @endif
        </div>
    </div>
</div>
@endsection