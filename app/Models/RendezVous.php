<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $fillable = [
        'numero_rdv',
        'patient_id',
        'professionnel_id',
        'structure_id',
        'date_heure',
        'duree_prevue',
        'date_heure_fin',
        'type',
        'modalite',
        'lieu_type',
        'specialite',
        'acte_id',
        'motif',
        'symptomes',
        'urgence_niveau',
        'antecedents_signales',
        'statut',
        'confirme_patient',
        'confirme_patient_at',
        'confirme_praticien',
        'confirme_praticien_at',
        'rappel_envoye',
        'rappel_envoye_at',
        'nombre_rappels',
        'raison_annulation',
        'annule_par',
        'annule_at',
        'reporte_de',
        'reporte_vers',
        'lien_teleconsultation',
        'room_id',
        'session_id',
        'debut_appel',
        'fin_appel',
        'duree_appel',
        'notes_patient',
        'instructions_preparation',
        'documents_requis',
        'montant_prevu',
        'paiement_confirme',
        'id',
    ];

    protected $casts = [
        'date_heure' => 'datetime',
        'date_heure_fin' => 'datetime',
        'symptomes' => 'array',
        'antecedents_signales' => 'array',
        'confirme_patient' => 'boolean',
        'confirme_praticien' => 'boolean',
        'rappel_envoye' => 'boolean',
        'confirme_patient_at' => 'datetime',
        'confirme_praticien_at' => 'datetime',
        'rappel_envoye_at' => 'datetime',
        'annule_at' => 'datetime',
        'debut_appel' => 'datetime',
        'fin_appel' => 'datetime',
        'instructions_preparation' => 'string',
        'documents_requis' => 'array',
        'paiement_confirme' => 'boolean',
    ];

    public static function generateNumero(): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $count = static::whereYear('created_at', $year)->whereMonth('created_at', now()->month)->count() + 1;
        return 'RDV-' . $year . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function professionnel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professionnel_id');
    }
}
