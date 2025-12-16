<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'department',
        'location',
        'type',
        'level',
        'description',
        'requirements',
        'benefits',
        'salary_min',
        'salary_max',
        'salary_currency',
        'is_remote',
        'is_active',
        'expires_at',
        'applications_count',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'applications_count' => 'integer',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_offer_id');
    }
}
