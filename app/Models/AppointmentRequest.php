<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'speciality',
        'mode',
        'structure_id',
        'practitioner_id',
        'numero_rdv',
        'preferred_date',
        'preferred_datetime',
        'notes',
        'status',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_datetime' => 'datetime',
    ];
}
