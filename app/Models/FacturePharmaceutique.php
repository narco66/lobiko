<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturePharmaceutique extends Model
{
    use HasFactory;

    protected $table = 'factures_pharmaceutiques';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'emise_le' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(CommandePharmaceutique::class, 'commande_pharmaceutique_id');
    }
}
