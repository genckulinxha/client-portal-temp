<?php

namespace App\Filament\Admin\Resources\ConversationResource\Pages;

use App\Filament\Admin\Resources\ConversationResource;
use App\Models\Message;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;

    public $newMessage = '';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ConversationResource\Widgets\ChatWidget::class,
        ];
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        Message::create([
            'conversation_id' => $this->record->id,
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';

        Notification::make()
            ->title('Message sent successfully')
            ->success()
            ->send();

        $this->dispatch('message-sent');
    }

    #[On('message-sent')]
    public function refreshMessages()
    {
        // This will trigger a re-render of the chat widget
    }
}