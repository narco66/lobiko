<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CompagnieAssurance extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'compagnies_assurance';

    protected $fillable = [
        'code_assureur',
        'nom_assureur',
        'nom_commercial',
        'type',
        'numero_agrement',
        'numero_fiscal',
        'registre_commerce',
        'adresse',
        'ville',
        'pays',
        'telephone',
        'email',
        'site_web',
        'email_medical',
        'telephone_medical',
        'fax',
        'api_active',
        'api_url',
        'api_key',
        'api_secret',
        'api_version',
        'api_endpoints',
        'tiers_payant',
        'pec_temps_reel',
        'delai_remboursement',
        'taux_commission',
        'documents_requis',
        'plafond_annuel_global',
        'plafond_consultation',
        'plafond_pharmacie',
        'plafond_hospitalisation',
        'actif',
        'partenaire',
        'date_partenariat',
        'fin_partenariat',
    ];

    protected $casts = [
        'api_active' => 'boolean',
        'tiers_payant' => 'boolean',
        'pec_temps_reel' => 'boolean',
        'actif' => 'boolean',
        'partenaire' => 'boolean',
        'api_endpoints' => 'array',
        'documents_requis' => 'array',
        'plafond_annuel_global' => 'decimal:2',
        'plafond_consultation' => 'decimal:2',
        'plafond_pharmacie' => 'decimal:2',
        'plafond_hospitalisation' => 'decimal:2',
        'taux_commission' => 'decimal:2',
        'date_partenariat' => 'date',
        'fin_partenariat' => 'date',
    ];
}
