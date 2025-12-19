<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FournisseurPharmaceutique extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fournisseurs_pharmaceutiques';

    protected $fillable = [
        'nom_fournisseur',
        'numero_licence',
        'telephone',
        'email',
        'adresse',
        'personne_contact',
        'telephone_contact',
        'categories_produits',
        'delai_livraison_jours',
        'montant_minimum_commande',
        'statut',
    ];

    protected $casts = [
        'categories_produits' => 'array',
        'delai_livraison_jours' => 'integer',
        'montant_minimum_commande' => 'decimal:2',
    ];

    public function pharmacies(): BelongsToMany
    {
        return $this->belongsToMany(Pharmacie::class, 'pharmacie_fournisseur')
            ->withPivot(['numero_compte_client', 'statut', 'credit_maximum', 'credit_utilise'])
            ->withTimestamps();
    }
}
