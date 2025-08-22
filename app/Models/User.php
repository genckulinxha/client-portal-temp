<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'role',
        'active',
        'hourly_rate',
        'phone',
        'bio',
        'bar_number',
        'permissions',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'hourly_rate' => 'decimal:2',
            'permissions' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->active && in_array($this->role, [
            'managing_partner', 'attorney', 'paralegal', 'intake_team', 'admin'
        ]);
    }

    // Attorney relationships
    public function attorneyCases(): HasMany
    {
        return $this->hasMany(CaseModel::class, 'attorney_id');
    }

    // Paralegal relationships
    public function paralegalCases(): HasMany
    {
        return $this->hasMany(CaseModel::class, 'paralegal_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to_user_id');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by_user_id');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by_user_id');
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function participantConversations(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class, 'participant_id')
            ->where('participant_type', 'user');
    }

    // Role helper methods
    public function isManagingPartner(): bool
    {
        return $this->role === 'managing_partner';
    }

    public function isAttorney(): bool
    {
        return $this->role === 'attorney';
    }

    public function isParalegal(): bool
    {
        return $this->role === 'paralegal';
    }

    public function isIntakeTeam(): bool
    {
        return $this->role === 'intake_team';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isManagingPartner() || $this->isAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }
}