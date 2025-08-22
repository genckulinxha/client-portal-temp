<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'all_day',
        'google_event_id',
        'synced_with_google',
        'last_google_sync',
        'user_id',
        'case_id',
        'client_id',
        'type',
        'location',
        'meeting_link',
        'attendees',
        'reminders',
        'status',
        'metadata',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'all_day' => 'boolean',
        'synced_with_google' => 'boolean',
        'last_google_sync' => 'datetime',
        'attendees' => 'array',
        'reminders' => 'array',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getDurationAttribute(): string
    {
        $diff = $this->start_datetime->diff($this->end_datetime);
        
        if ($diff->days > 0) {
            return $diff->days . ' day(s)';
        }
        
        return $diff->format('%H:%I');
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_datetime->isFuture();
    }

    public function getIsOngoingAttribute(): bool
    {
        $now = now();
        return $this->start_datetime->isPast() && $this->end_datetime->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->end_datetime->isPast();
    }
    

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_datetime', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_datetime', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('start_datetime', [$start, $end]);
    }

    public function getFormattedTitleAttribute(): string
    {
        $title = $this->title;
        
        if ($this->client) {
            $title .= ' - ' . $this->client->first_name . ' ' . $this->client->last_name;
        }
        
        if ($this->case) {
            $title .= ' (' . $this->case->case_number . ')';
        }
        
        return $title;
    }
}