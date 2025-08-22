<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Defendant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'case_id',
        'name',
        'type',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'email',
        'contact_person',
        'violation_details',
        'additional_info',
    ];

    protected $casts = [
        'additional_info' => 'array',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code
        ]);
        
        return implode(', ', $parts);
    }
}