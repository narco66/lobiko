<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActeMedical extends Model
{
    protected $table = 'actes_medicaux';

    protected $fillable = [
        'code_acte',
        'libelle',
        'description',
        'categorie',
        'specialite',
        'tarif_base',
        'duree_prevue',
        'urgence_possible',
        'teleconsultation_possible',
        'domicile_possible',
        'prerequis',
        'contre_indications',
        'age_minimum',
        'age_maximum',
        'sexe_requis',
        'equipements_requis',
        'consommables',
        'tarif_urgence',
        'tarif_weekend',
        'tarif_nuit',
        'tarif_domicile',
        'remboursable',
        'taux_remboursement_base',
        'code_securite_sociale',
        'actif',
        'date_debut_validite',
        'date_fin_validite',
    ];

    protected $casts = [
        'urgence_possible' => 'boolean',
        'teleconsultation_possible' => 'boolean',
        'domicile_possible' => 'boolean',
        'remboursable' => 'boolean',
        'actif' => 'boolean',
        'prerequis' => 'array',
        'contre_indications' => 'array',
        'equipements_requis' => 'array',
        'consommables' => 'array',
        'date_debut_validite' => 'date',
        'date_fin_validite' => 'date',
    ];
}
