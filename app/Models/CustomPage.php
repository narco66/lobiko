<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'template',
        'meta_data',
        'is_published',
        'in_menu',
        'menu_order',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'is_published' => 'boolean',
        'in_menu' => 'boolean',
        'menu_order' => 'integer',
    ];
}
