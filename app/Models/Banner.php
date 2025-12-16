<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'button_text',
        'position',
        'start_date',
        'end_date',
        'is_active',
        'click_count',
        'view_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'click_count' => 'integer',
        'view_count' => 'integer',
    ];
}
