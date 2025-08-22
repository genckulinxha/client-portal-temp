<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'default_tasks',
    ];

    protected $casts = [
        'default_tasks' => 'array',
    ];

    // Relationships
    public function cases(): HasMany
    {
        return $this->hasMany(LegalCase::class);
    }
}