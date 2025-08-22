<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cases';

    protected $fillable = [
        'client_id',
        'attorney_id',
        'paralegal_id',
        'case_number',
        'case_title',
        'case_type',
        'status',
        'description',
        'potential_damages',
        'settlement_amount',
        'statute_limitations',
        'filed_date',
        'closed_date',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'potential_damages' => 'decimal:2',
        'settlement_amount' => 'decimal:2',
        'statute_limitations' => 'date',
        'filed_date' => 'date',
        'closed_date' => 'date',
        'metadata' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function attorney(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attorney_id');
    }

    public function paralegal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paralegal_id');
    }

    public function defendants(): HasMany
    {
        return $this->hasMany(Defendant::class, 'case_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'case_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'case_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class, 'case_id');
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class, 'case_id');
    }

    public function getTotalTimeAttribute(): float
    {
        return $this->timeEntries->sum('hours');
    }

    public function getTotalBillableAmountAttribute(): float
    {
        return $this->timeEntries->where('billable', true)->sum('total_amount');
    }
}