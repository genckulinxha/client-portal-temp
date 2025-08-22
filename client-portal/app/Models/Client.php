<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Client extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'date_of_birth',
        'ssn',
        'status',
        'portal_access',
        'portal_password',
        'intake_data',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'portal_access' => 'boolean',
        'intake_data' => 'array',
        'last_portal_login' => 'datetime',
    ];

    protected $hidden = [
        'ssn',
        'portal_password',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function setPortalPasswordAttribute($value): void
    {
        if ($value) {
            $this->attributes['portal_password'] = Hash::make($value);
        }
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CaseModel::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to_client_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by_client_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}