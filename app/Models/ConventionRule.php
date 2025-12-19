<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConventionRule extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'exclusions' => 'array',
        'prior_authorization_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function convention()
    {
        return $this->belongsTo(Convention::class);
    }
}
