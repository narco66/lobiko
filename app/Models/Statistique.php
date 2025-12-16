<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistique extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'value',
        'unit',
        'icon',
        'is_visible',
        'order',
    ];

    protected $casts = [
        'value' => 'integer',
        'is_visible' => 'boolean',
        'order' => 'integer',
    ];
}
