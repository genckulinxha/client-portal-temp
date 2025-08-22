<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'filename',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'file_hash',
        'type',
        'client_viewable',
        'is_confidential',
        'case_id',
        'client_id',
        'task_id',
        'uploaded_by_user_id',
        'uploaded_by_client_id',
        'version',
        'parent_document_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'client_viewable' => 'boolean',
        'is_confidential' => 'boolean',
        'file_size' => 'integer',
        'version' => 'integer',
        'metadata' => 'array',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function uploadedByClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'uploaded_by_client_id');
    }

    public function parentDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(Document::class, 'parent_document_id');
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getUploaderAttribute(): ?Model
    {
        return $this->uploaded_by_user_id 
            ? $this->uploadedByUser 
            : $this->uploadedByClient;
    }
}