<?php

namespace App\Filament\Admin\Resources\ConversationResource\Widgets;

use App\Models\Conversation;
use App\Models\Message;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class ChatWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.chat-widget';

    public Conversation $conversation;
    public $newMessage = '';

    public function mount($record)
    {
        $this->conversation = $record;
        $this->markAsRead();
    }

    public function getMessages()
    {
        return $this->conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
            'message' => $this->newMessage,
        ]);

        $this->reset('newMessage');
        $this->dispatch('message-sent');
    }

    #[On('message-sent')]
    public function refreshMessages()
    {
        $this->markAsRead();
    }

    private function markAsRead()
    {
        $this->conversation->markAsRead('user', auth()->id());
    }

    protected function getViewData(): array
    {
        return [
            'messages' => $this->getMessages(),
            'currentUserId' => auth()->id(),
        ];
    }
}