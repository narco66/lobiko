<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'is_subscribed',
        'token',
        'confirmed_at',
        'unsubscribed_at',
        'ip_address',
    ];

    protected $casts = [
        'is_subscribed' => 'boolean',
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];
}
