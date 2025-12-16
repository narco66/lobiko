<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'city',
        'location',
        'emergency_type',
        'notes',
        'status',
    ];
}
