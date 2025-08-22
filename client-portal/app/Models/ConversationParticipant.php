<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'participant_type',
        'participant_id',
        'joined_at',
        'last_read_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function getParticipantAttribute()
    {
        if ($this->participant_type === 'user') {
            return User::find($this->participant_id);
        }
        return Client::find($this->participant_id);
    }

    public function getParticipantNameAttribute(): string
    {
        $participant = $this->participant;
        
        if ($this->participant_type === 'user') {
            return $participant->name ?? 'Unknown User';
        }
        
        return $participant->full_name ?? 'Unknown Client';
    }
}