<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'case_id',
        'task_id',
        'hours',
        'description',
        'date',
        'start_time',
        'end_time',
        'billable',
        'hourly_rate',
        'total_amount',
        'status',
        'metadata',
    ];

    protected $casts = [
        'hours' => 'decimal:1',
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
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

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (TimeEntry $timeEntry) {
            if ($timeEntry->billable && $timeEntry->hourly_rate && $timeEntry->hours) {
                $timeEntry->total_amount = $timeEntry->hours * $timeEntry->hourly_rate;
            }
            
            if (!$timeEntry->hourly_rate && $timeEntry->user) {
                $timeEntry->hourly_rate = $timeEntry->user->hourly_rate;
            }
        });
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        $start = $this->start_time;
        $end = $this->end_time;
        $diff = $start->diff($end);

        return $diff->format('%H:%I');
    }
}