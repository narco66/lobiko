<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'website',
        'description',
        'type',
        'order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
}
