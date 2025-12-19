<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivraisonPharmaceutique extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'livraisons_pharmaceutiques';

    protected $fillable = [
        'commande_pharmaceutique_id',
        'livreur_id',
        'numero_livraison',
        'statut',
        'date_depart',
        'date_arrivee_prevue',
        'date_livraison',
        'nom_receptionnaire',
        'telephone_receptionnaire',
        'signature_receptionnaire',
        'photo_livraison',
        'commentaire_livreur',
        'motif_echec',
        'tracking_gps',
        'distance_parcourue_km',
    ];

    protected $casts = [
        'date_depart' => 'datetime',
        'date_arrivee_prevue' => 'datetime',
        'date_livraison' => 'datetime',
        'tracking_gps' => 'array',
        'distance_parcourue_km' => 'decimal:2',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(CommandePharmaceutique::class, 'commande_pharmaceutique_id');
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }
}
