<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevisPharmaceutique extends Model
{
    use HasFactory;

    protected $table = 'devis_pharmaceutiques';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'lignes' => 'array',
        'envoye_at' => 'datetime',
        'accepte_at' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(CommandePharmaceutique::class, 'commande_pharmaceutique_id');
    }
}
