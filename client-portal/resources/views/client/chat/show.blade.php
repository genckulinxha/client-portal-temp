@extends('client.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Conversation</h1>
            <p class="mt-2 text-gray-600">
                Case: {{ $conversation->case->case_number }}
                @if($conversation->case->case_title)
                    - {{ $conversation->case->case_title }}
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('client.chat.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Back to Messages
            </a>
        </div>
    </div>

    @livewire('chat.chat-box', ['conversation' => $conversation, 'client' => $client])
</div>
@endsection