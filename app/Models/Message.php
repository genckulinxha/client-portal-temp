<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message',
        'attachments',
        'is_read',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function getSenderAttribute()
    {
        if ($this->sender_type === 'user') {
            return User::find($this->sender_id);
        }
        return Client::find($this->sender_id);
    }

    public function getSenderNameAttribute(): string
    {
        $sender = $this->sender;
        
        if ($this->sender_type === 'user') {
            return $sender->name ?? 'Unknown User';
        }
        
        return $sender->full_name ?? 'Unknown Client';
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    protected static function booted()
    {
        static::created(function ($message) {
            $message->conversation->update([
                'last_message_at' => $message->created_at,
            ]);

            // Send notifications to participants (except sender)
            $conversation = $message->conversation;
            $participants = $conversation->participants()
                ->where(function ($query) use ($message) {
                    $query->where('participant_type', '!=', $message->sender_type)
                          ->orWhere('participant_id', '!=', $message->sender_id);
                })
                ->get();

            foreach ($participants as $participant) {
                $notifiable = $participant->participant;
                if ($notifiable) {
                    $notifiable->notify(new \App\Notifications\NewMessageNotification($message, $conversation));
                }
            }
        });
    }
}