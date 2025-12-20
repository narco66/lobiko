<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrilleTarifaire extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'grilles_tarifaires';

    protected $fillable = [
        'nom_grille',
        'type_client',
        'zone',
        'structure_id',
        'applicable_a',
        'element_id',
        'coefficient_multiplicateur',
        'majoration_fixe',
        'taux_remise',
        'tva_applicable',
        'quantite_min',
        'quantite_max',
        'montant_min',
        'montant_max',
        'date_debut',
        'date_fin',
        'actif',
        'priorite',
    ];

    protected $casts = [
        'coefficient_multiplicateur' => 'decimal:2',
        'majoration_fixe' => 'decimal:2',
        'taux_remise' => 'decimal:2',
        'tva_applicable' => 'decimal:2',
        'quantite_min' => 'integer',
        'quantite_max' => 'integer',
        'montant_min' => 'decimal:2',
        'montant_max' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'actif' => 'boolean',
        'priorite' => 'integer',
    ];

    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'structure_id');
    }
}
