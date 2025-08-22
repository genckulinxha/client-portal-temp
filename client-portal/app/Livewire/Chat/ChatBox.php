<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;
use Livewire\Attributes\On;

class ChatBox extends Component
{
    public Conversation $conversation;
    public $newMessage = '';
    public $client;

    public function mount(Conversation $conversation, $client)
    {
        $this->conversation = $conversation;
        $this->client = $client;
        $this->markAsRead();
    }

    public function render()
    {
        $messages = $this->conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.chat.chat-box', [
            'messages' => $messages,
        ]);
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_type' => 'client',
            'sender_id' => $this->client->id,
            'message' => $this->newMessage,
        ]);

        $this->reset('newMessage');
        $this->markAsRead();
        $this->dispatch('message-sent');
    }

    #[On('message-sent')]
    public function refreshMessages()
    {
        $this->markAsRead();
    }

    public function markAsRead()
    {
        $this->conversation->markAsRead('client', $this->client->id);
    }

    public function getListeners()
    {
        return [
            'echo:conversation.' . $this->conversation->id . ',MessageSent' => 'refreshMessages',
        ];
    }
}