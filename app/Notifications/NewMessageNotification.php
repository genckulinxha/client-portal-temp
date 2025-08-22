<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Message $message,
        public Conversation $conversation
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $senderName = $this->message->sender_name;
        $caseNumber = $this->conversation->case->case_number;
        
        if ($this->message->sender_type === 'client') {
            $subject = "New message from {$senderName} - Case {$caseNumber}";
            $greeting = "You have received a new message from your client {$senderName}.";
            $actionUrl = route('filament.admin.resources.conversations.view', $this->conversation);
            $actionText = 'View in Admin Portal';
        } else {
            $subject = "New message from your attorney - Case {$caseNumber}";
            $greeting = "You have received a new message from {$senderName}.";
            $actionUrl = route('client.chat.show', $this->conversation);
            $actionText = 'View Message';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line("**Case:** {$caseNumber}")
            ->line("**Message:**")
            ->line('"' . $this->message->message . '"')
            ->action($actionText, $actionUrl)
            ->line('Thank you for using our client portal.');
    }

    public function toArray($notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
            'sender_name' => $this->message->sender_name,
            'case_number' => $this->conversation->case->case_number,
            'message_preview' => substr($this->message->message, 0, 100),
        ];
    }
}