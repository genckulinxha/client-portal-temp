@extends('client.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Messages</h1>
        <p class="mt-2 text-gray-600">Communicate with your attorney about your cases.</p>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
                    @if($conversations->count() > 0)
                        <div class="space-y-4">
                            @foreach($conversations as $conversation)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <a href="{{ route('client.chat.show', $conversation) }}" class="block">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-medium text-gray-900">
                                                            Case: {{ $conversation->case->case_number }}
                                                            @if($conversation->case->case_title)
                                                                - {{ $conversation->case->case_title }}
                                                            @endif
                                                        </h3>
                                                        @if($conversation->subject)
                                                            <p class="text-sm text-gray-600 mt-1">
                                                                {{ $conversation->subject }}
                                                            </p>
                                                        @endif
                                                        
                                                        @if($conversation->latestMessage->first())
                                                            <div class="mt-2 text-sm text-gray-600">
                                                                <span class="font-medium">{{ $conversation->latestMessage->first()->sender_name }}:</span>
                                                                {{ Str::limit($conversation->latestMessage->first()->message, 100) }}
                                                            </div>
                                                        @else
                                                            <div class="mt-2 text-sm text-gray-500">
                                                                No messages yet
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex flex-col items-end space-y-2">
                                                        @if($conversation->hasUnreadMessages('client', $client->id))
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                New
                                                            </span>
                                                        @endif
                                                        
                                                        <div class="text-sm text-gray-500">
                                                            {{ $conversation->messages_count }} {{ Str::plural('message', $conversation->messages_count) }}
                                                        </div>
                                                        
                                                        @if($conversation->last_message_at)
                                                            <div class="text-xs text-gray-500">
                                                                {{ $conversation->last_message_at->diffForHumans() }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations yet</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Your attorney will start conversations with you about your cases.
                            </p>
                        </div>
                    @endif
                </div>
        </div>
    </div>
</div>
@endsection