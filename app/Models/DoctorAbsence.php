<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DoctorAbsence extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'doctor_id',
        'structure_id',
        'start_at',
        'end_at',
        'motif',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function structure()
    {
        return $this->belongsTo(MedicalStructure::class, 'structure_id');
    }
}
