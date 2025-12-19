<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
        'date_echeance_remboursement' => 'date',
        'periode_soins_debut' => 'date',
        'periode_soins_fin' => 'date',
    ];

    public function convention()
    {
        return $this->belongsTo(Convention::class);
    }

    public function items()
    {
        return $this->hasMany(ClaimItem::class);
    }
}
