<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeleconsultationFile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'session_id',
        'uploader_id',
        'original_name',
        'path',
        'mime_type',
        'size',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TeleconsultationSession::class, 'session_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
