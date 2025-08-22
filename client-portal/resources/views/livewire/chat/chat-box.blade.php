<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">
                Case: {{ $conversation->case->case_number }}
            </h3>
            @if($conversation->subject)
                <p class="text-sm text-gray-600">{{ $conversation->subject }}</p>
            @endif
        </div>
    </div>

    <!-- Messages Container -->
    <div class="h-96 overflow-y-auto p-4 space-y-4" id="messages-container" wire:poll.5s="refreshMessages">
        @forelse($messages as $message)
            <div class="flex {{ $message->sender_type === 'client' && $message->sender_id === $client->id ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md">
                    <div class="px-4 py-2 rounded-lg {{ $message->sender_type === 'client' && $message->sender_id === $client->id ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-900' }}">
                        <div class="text-xs opacity-75 mb-1">
                            {{ $message->sender_name }} • {{ $message->created_at->format('M j, g:i A') }}
                        </div>
                        <div class="text-sm break-words">
                            {{ $message->message }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium">No messages yet</h3>
                <p class="mt-1 text-sm">Start the conversation with your attorney!</p>
            </div>
        @endforelse
    </div>

    <!-- Message Input -->
    <div class="px-4 py-4 border-t border-gray-200">
        <form wire:submit.prevent="sendMessage" class="flex space-x-3">
            <div class="flex-1 min-w-0">
                <input
                    type="text"
                    wire:model="newMessage"
                    wire:keydown.enter="sendMessage"
                    placeholder="Type your message..."
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    maxlength="1000"
                >
            </div>
            <button
                type="submit"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed w-[100px] min-w-[100px]"
                wire:loading.attr="disabled"
                wire:target="sendMessage"
            >
                <span wire:loading.remove wire:target="sendMessage">Send</span>
                <span wire:loading wire:target="sendMessage" class="inline-flex items-center justify-center w-full">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 814 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                </span>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', function () {
        Livewire.on('message-sent', function () {
            setTimeout(() => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
                
                // Clear the input field to ensure it's empty
                const messageInput = document.querySelector('input[wire\\:model="newMessage"]');
                if (messageInput) {
                    messageInput.value = '';
                }
            }, 100);
        });
    });

    // Auto-scroll to bottom on page load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 200);

        // Auto-scroll when typing in the message input
        const messageInput = document.querySelector('input[wire\\:model="newMessage"]');
        if (messageInput) {
            messageInput.addEventListener('input', function() {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
            
            messageInput.addEventListener('focus', function() {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    });
</script>