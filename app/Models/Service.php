<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'full_description',
        'icon',
        'image',
        'order',
        'is_active',
        'is_featured',
        'features',
        'base_price',
        'price_unit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
        'features' => 'array',
        'base_price' => 'decimal:2',
    ];
}
