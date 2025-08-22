@extends('client.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ $client->first_name }}!</h1>
        <p class="mt-2 text-gray-600">Here's an overview of your case progress and pending tasks.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Active Cases -->
        <div class="stats-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Cases</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $cases->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="stats-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Tasks</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $pendingTasks->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress -->
        <div class="stats-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Task Progress</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $progressPercentage }}%</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Documents -->
        <div class="stats-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Documents</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $recentDocuments->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Pending Tasks -->
        <div class="card">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pending Tasks</h3>
                @if($pendingTasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingTasks->take(5) as $task)
                            <div class="border rounded-lg p-4 {{ $task->is_overdue ? 'border-red-200 bg-red-50' : 'border-gray-200' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $task->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                                        @if($task->case)
                                            <p class="text-xs text-blue-600 mt-1">Case: {{ $task->case->case_number }}</p>
                                        @endif
                                        @if($task->due_date)
                                            <p class="text-xs {{ $task->is_overdue ? 'text-red-600' : 'text-gray-500' }} mt-1">
                                                Due: {{ $task->due_date->format('M j, Y g:i A') }}
                                                @if($task->is_overdue)
                                                    (Overdue)
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $task->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $task->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('client.tasks.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View all tasks →
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No pending tasks at this time.</p>
                @endif
            </div>
        </div>

        <!-- My Cases -->
        <div class="card">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">My Cases</h3>
                @if($cases->count() > 0)
                    <div class="space-y-4">
                        @foreach($cases as $case)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $case->case_title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">Case #: {{ $case->case_number }}</p>
                                        <p class="text-sm text-gray-600">Attorney: {{ $case->attorney->name }}</p>
                                        @if($case->paralegal)
                                            <p class="text-sm text-gray-600">Paralegal: {{ $case->paralegal->name }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $case->status === 'intake' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $case->status === 'investigation' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $case->status === 'litigation' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $case->status === 'settlement' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $case->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($case->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No active cases.</p>
                @endif
            </div>
        </div>

        <!-- Recent Documents -->
        <div class="card">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Documents</h3>
                @if($recentDocuments->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentDocuments as $document)
                            <div class="flex items-center justify-between border-b pb-2">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $document->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $document->created_at->format('M j, Y') }}</p>
                                </div>
                                <a href="{{ route('client.documents.download', $document) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('client.documents.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View all documents →
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No documents available.</p>
                @endif
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="card">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Upcoming Events</h3>
                @if($upcomingEvents->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingEvents as $event)
                            <div class="border-l-4 border-blue-500 pl-3">
                                <p class="text-sm font-medium text-gray-900">{{ $event->title }}</p>
                                <p class="text-xs text-gray-600">{{ $event->start_datetime->format('M j, Y g:i A') }}</p>
                                @if($event->location)
                                    <p class="text-xs text-gray-500">{{ $event->location }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No upcoming events scheduled.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection