<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'prescription_code',
        'delivery_mode',
        'address',
        'notes',
        'status',
    ];
}
