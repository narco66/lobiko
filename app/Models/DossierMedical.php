<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DossierMedical extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'dossiers_medicaux';

    protected $fillable = [
        'patient_id',
        'numero_dossier',
        'groupe_sanguin',
        'rhesus',
        'allergies',
        'antecedents_medicaux',
        'antecedents_chirurgicaux',
        'antecedents_familiaux',
        'vaccinations',
        'tabac',
        'cigarettes_jour',
        'alcool',
        'activite_physique',
        'regime_alimentaire',
        'traitements_chroniques',
        'medicaments_actuels',
        'derniere_mise_jour_traitement',
        'date_dernieres_regles',
        'enceinte',
        'nombre_grossesses',
        'nombre_enfants',
        'contraception',
        'tension_habituelle_sys',
        'tension_habituelle_dia',
        'poids_habituel',
        'taille_cm',
        'imc',
        'contact_urgence_nom',
        'contact_urgence_telephone',
        'contact_urgence_lien',
        'acces_autorises',
        'partage_autorise',
        'elements_caches',
        'actif',
        'derniere_consultation',
        'nombre_consultations',
        'notes_privees',
    ];

    protected $casts = [
        'derniere_consultation' => 'datetime',
        'derniere_mise_jour_traitement' => 'date',
        'date_dernieres_regles' => 'date',
        'enceinte' => 'boolean',
        'partage_autorise' => 'boolean',
        'actif' => 'boolean',
        'acces_autorises' => 'array',
        'elements_caches' => 'array',
        'tension_habituelle_sys' => 'decimal:2',
        'tension_habituelle_dia' => 'decimal:2',
        'poids_habituel' => 'decimal:2',
        'taille_cm' => 'decimal:2',
        'imc' => 'decimal:2',
        'nombre_consultations' => 'integer',
        'allergies' => 'encrypted:array',
        'antecedents_medicaux' => 'encrypted:array',
        'antecedents_chirurgicaux' => 'encrypted:array',
        'antecedents_familiaux' => 'encrypted:array',
        'traitements_chroniques' => 'encrypted:array',
        'medicaments_actuels' => 'encrypted:array',
        'vaccinations' => 'encrypted:array',
        'notes_privees' => 'encrypted',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
