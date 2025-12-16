<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeleconsultationSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'consultation_id',
        'status',
        'provider',
        'room_name',
        'patient_token',
        'practitioner_token',
        'token_expires_at',
        'started_at',
        'ended_at',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TeleconsultationMessage::class, 'session_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(TeleconsultationFile::class, 'session_id');
    }
}
