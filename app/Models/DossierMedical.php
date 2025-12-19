<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class DossierMedical extends Model
{
    protected $table = 'dossiers_medicaux';

    protected $fillable = [
        'patient_id',
        'numero_dossier',
        'tension_habituelle_sys',
        'tension_habituelle_dia',
        'poids_habituel',
        'taille_cm',
        'imc',
        'derniere_consultation',
        'nombre_consultations',
        'allergies',
        'antecedents',
        'traitements_en_cours',
        'vaccinations',
        'historique_familial',
        'habitudes_vie',
        'notes_privees',
    ];

    protected $casts = [
        'derniere_consultation' => 'datetime',
        'tension_habituelle_sys' => 'decimal:2',
        'tension_habituelle_dia' => 'decimal:2',
        'poids_habituel' => 'decimal:2',
        'taille_cm' => 'decimal:2',
        'imc' => 'decimal:2',
        'nombre_consultations' => 'integer',
        'allergies' => 'encrypted:array',
        'antecedents' => 'encrypted:array',
        'traitements_en_cours' => 'encrypted:array',
        'vaccinations' => 'encrypted:array',
        'historique_familial' => 'encrypted:array',
        'habitudes_vie' => 'encrypted:array',
        'notes_privees' => 'encrypted',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
