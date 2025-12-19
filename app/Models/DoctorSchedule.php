<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DoctorSchedule extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'doctor_id',
        'structure_id',
        'day_of_week',
        'date',
        'start_time',
        'end_time',
        'is_recurring',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'starts_at' => 'date',
        'ends_at' => 'date',
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
