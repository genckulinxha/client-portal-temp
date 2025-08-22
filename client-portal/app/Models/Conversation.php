<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'client_id',
        'created_by_type',
        'created_by_id',
        'status',
        'subject',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function getCreatedByAttribute()
    {
        if ($this->created_by_type === 'user') {
            return User::find($this->created_by_id);
        }
        return Client::find($this->created_by_id);
    }

    public function scopeForCase($query, $caseId)
    {
        return $query->where('case_id', $caseId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function addParticipant($participantType, $participantId)
    {
        return $this->participants()->firstOrCreate([
            'participant_type' => $participantType,
            'participant_id' => $participantId,
        ], [
            'joined_at' => now(),
        ]);
    }

    public function markAsRead($participantType, $participantId)
    {
        $this->participants()
            ->where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->update(['last_read_at' => now()]);
    }

    public function hasUnreadMessages($participantType, $participantId): bool
    {
        $participant = $this->participants()
            ->where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->first();

        if (!$participant || !$participant->last_read_at) {
            return $this->messages()->exists();
        }

        return $this->messages()
            ->where('created_at', '>', $participant->last_read_at)
            ->exists();
    }
}