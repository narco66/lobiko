<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'question',
        'answer',
        'order',
        'is_published',
        'helpful_count',
        'not_helpful_count',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_published' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
    ];
}
