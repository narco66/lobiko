<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevisLigne extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'devis_id',
        'ordre',
        'type',
        'element_id',
        'code',
        'libelle',
        'description',
        'quantite',
        'prix_unitaire',
        'montant_ht',
        'taux_tva',
        'montant_tva',
        'montant_ttc',
        'taux_remise',
        'montant_remise',
        'taux_majoration',
        'montant_majoration',
        'montant_final',
        'remboursable',
        'taux_couverture',
        'montant_couvert',
        'reste_a_charge',
    ];

    protected $casts = [
        'remboursable' => 'boolean',
        'prix_unitaire' => 'decimal:2',
        'montant_ht' => 'decimal:2',
        'taux_tva' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'taux_remise' => 'decimal:2',
        'montant_remise' => 'decimal:2',
        'taux_majoration' => 'decimal:2',
        'montant_majoration' => 'decimal:2',
        'montant_final' => 'decimal:2',
        'taux_couverture' => 'decimal:2',
        'montant_couvert' => 'decimal:2',
        'reste_a_charge' => 'decimal:2',
    ];
}
