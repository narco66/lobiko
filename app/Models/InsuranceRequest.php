<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'policy_number',
        'insurer',
        'request_type',
        'notes',
        'status',
    ];
}
