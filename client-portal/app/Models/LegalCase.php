<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cases';

    protected $fillable = [
        'client_id',
        'case_type_id',
        'case_number',
        'case_title',
        'status',
        'attorney_id',
        'paralegal_id',
        'damages_category',
        'settlement_amount',
        'attorney_fees',
        'case_expenses',
        'referral_fee_percentage',
        'referral_fee_amount',
        'date_opened',
        'date_closed',
        'notes',
    ];

    protected $casts = [
        'settlement_amount' => 'decimal:2',
        'attorney_fees' => 'decimal:2',
        'case_expenses' => 'decimal:2',
        'referral_fee_percentage' => 'decimal:2',
        'referral_fee_amount' => 'decimal:2',
        'date_opened' => 'date',
        'date_closed' => 'date',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function caseType(): BelongsTo
    {
        return $this->belongsTo(CaseType::class);
    }

    public function assignedAttorney(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attorney_id');
    }

    public function assignedParalegal(): BelongsTo
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

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'case_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active_litigation', 'discovery', 'settlement_negotiations']);
    }

    public function scopeByAttorney($query, $attorneyId)
    {
        return $query->where('attorney_id', $attorneyId);
    }

    // Accessors
    public function getNetSettlementAttribute(): ?float
    {
        if (!$this->settlement_amount) return null;
        
        return $this->settlement_amount - $this->attorney_fees - $this->case_expenses - $this->referral_fee_amount;
    }

    public function getCaseDisplayNameAttribute(): string
    {
        return "{$this->case_number} - {$this->client->full_name}";
    }
}