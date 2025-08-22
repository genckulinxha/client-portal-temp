<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Chat with {{ $this->conversation->client->full_name }}
        </x-slot>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Case: {{ $this->conversation->case->case_number }}
                    </h3>
                    @if($this->conversation->subject)
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $this->conversation->subject }}</p>
                    @endif
                </div>
            </div>

            <!-- Messages Container -->
            <div class="h-96 overflow-y-auto p-4 space-y-4 bg-gray-100 dark:bg-gray-900" id="messages-container" wire:poll.5s="refreshMessages">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_type === 'user' && $message->sender_id === $currentUserId ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            @if($message->sender_type === 'user')
                                <!-- Admin message (blue background) -->
                                <div class="px-4 py-3 rounded-lg shadow-md bg-blue-600 dark:bg-blue-700 text-white border border-blue-500 dark:border-blue-600 {{ $message->sender_id === $currentUserId ? 'ml-12' : 'mr-12' }}">
                                    <div class="text-xs mb-1 text-blue-100 dark:text-blue-200">
                                        {{ $message->sender_name }} • {{ $message->created_at->format('M j, g:i A') }}
                                    </div>
                                    <div class="text-sm leading-relaxed text-white font-medium">
                                        {{ $message->message }}
                                    </div>
                                </div>
                            @else
                                <!-- Client message (white/gray background) -->
                                <div class="px-4 py-3 rounded-lg shadow-md bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 mr-12">
                                    <div class="text-xs mb-1 text-gray-600 dark:text-gray-400 font-medium">
                                        {{ $message->sender_name }} • {{ $message->created_at->format('M j, g:i A') }}
                                    </div>
                                    <div class="text-sm leading-relaxed text-gray-900 dark:text-gray-100">
                                        {{ $message->message }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No messages yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start the conversation with your client!</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <form wire:submit.prevent="sendMessage" class="flex items-end gap-4">
                    <div class="flex-1 mr-4">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                wire:model="newMessage"
                                wire:keydown.enter="sendMessage"
                                placeholder="Type your message..."
                                maxlength="1000"
                                wire:loading.attr="disabled"
                                wire:target="sendMessage"
                            />
                        </x-filament::input.wrapper>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <x-filament::button
                            type="submit"
                            color="primary"
                            size="md"
                            wire:loading.attr="disabled"
                            wire:target="sendMessage"
                            class="!w-[100px] !min-w-[100px]"
                        >
                        <span wire:loading.remove wire:target="sendMessage">Send</span>
                        <span wire:loading wire:target="sendMessage" class="inline-flex items-center justify-center w-full">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    </x-filament::section>

    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on('message-sent', function () {
                setTimeout(() => {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                    
                    // Clear the input field to ensure it's empty
                    const messageInput = document.querySelector('[wire\\:model="newMessage"]');
                    if (messageInput) {
                        messageInput.value = '';
                    }
                }, 100);
            });
        });

        // Auto-scroll to bottom on page load and after updates
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 200);
        });

        // Auto-scroll when new messages are polled
        document.addEventListener('livewire:updated', function () {
            setTimeout(() => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 100);
        });

        // Auto-scroll when typing in the message input
        document.addEventListener('DOMContentLoaded', function() {
            function setupAutoScroll() {
                const messageInput = document.querySelector('[wire\\:model="newMessage"]') || 
                                   document.querySelector('input[wire\\:model="newMessage"]') ||
                                   document.querySelector('.fi-input input[wire\\:model="newMessage"]');
                
                if (messageInput) {
                    // Remove existing listeners to prevent duplicates
                    messageInput.removeEventListener('input', scrollToBottom);
                    messageInput.removeEventListener('focus', scrollToBottom);
                    messageInput.removeEventListener('keydown', handleKeydown);
                    
                    // Add listeners
                    messageInput.addEventListener('input', scrollToBottom);
                    messageInput.addEventListener('focus', scrollToBottom);
                    messageInput.addEventListener('keydown', handleKeydown);
                }
            }
            
            function scrollToBottom() {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }
            
            function handleKeydown(event) {
                if (event.key === 'Enter') {
                    setTimeout(() => {
                        scrollToBottom();
                    }, 100);
                }
            }
            
            // Initial setup
            setupAutoScroll();
            
            // Re-setup after Livewire updates
            document.addEventListener('livewire:updated', function() {
                setTimeout(setupAutoScroll, 50);
            });
        });
    </script>
</x-filament-widgets::widget>