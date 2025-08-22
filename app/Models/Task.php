<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'priority',
        'board_order',
        'assigned_to_user_id',
        'assigned_to_client_id',
        'case_id',
        'created_by_user_id',
        'due_date',
        'completed_at',
        'requirements',
        'completion_notes',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'requirements' => 'array',
    ];

    public function assignedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function assignedToClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'assigned_to_client_id');
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getAssigneeAttribute(): ?Model
    {
        if ($this->assigned_to_user_id) {
            return $this->assignedToUser()->first();
        }
        
        if ($this->assigned_to_client_id) {
            return $this->assignedToClient()->first();
        }
        
        return null;
    }

    public function markCompleted(string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $notes,
        ]);
    }
}