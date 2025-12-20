<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Forfait extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'forfaits';

    protected $fillable = [
        'code_forfait',
        'nom_forfait',
        'description',
        'categorie',
        'prix_forfait',
        'duree_validite',
        'nombre_seances',
        'actes_inclus',
        'produits_inclus',
        'examens_inclus',
        'age_minimum',
        'age_maximum',
        'sexe_requis',
        'pathologies_cibles',
        'remboursable',
        'taux_remboursement',
        'actif',
    ];

    protected $casts = [
        'prix_forfait' => 'decimal:2',
        'duree_validite' => 'integer',
        'nombre_seances' => 'integer',
        'actes_inclus' => 'array',
        'produits_inclus' => 'array',
        'examens_inclus' => 'array',
        'pathologies_cibles' => 'array',
        'age_minimum' => 'integer',
        'age_maximum' => 'integer',
        'remboursable' => 'boolean',
        'taux_remboursement' => 'decimal:2',
        'actif' => 'boolean',
    ];
}
