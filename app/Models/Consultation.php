<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ActeMedical;
use App\Models\Notification;
use App\Models\RendezVous;
use App\Models\User;

class Consultation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'numero_consultation',
        'rendez_vous_id',
        'patient_id',
        'professionnel_id',
        'structure_id',
        'date_consultation',
        'heure_debut',
        'heure_fin',
        'duree_effective',
        'type',
        'modalite',
        'motif_consultation',
        'histoire_maladie',
        'symptomes_declares',
        'debut_symptomes',
        'signes_vitaux',
        'examen_general',
        'examen_par_appareil',
        'examens_complementaires_demandes',
        'diagnostic_principal',
        'diagnostics_secondaires',
        'code_cim10',
        'certitude_diagnostic',
        'conduite_a_tenir',
        'actes_realises',
        'prescriptions',
        'examens_prescrits',
        'ordonnance_delivree',
        'arret_travail',
        'duree_arret_travail',
        'certificat_medical',
        'orientation_specialiste',
        'specialiste_oriente',
        'hospitalisation',
        'structure_hospitalisation',
        'prochain_rdv',
        'recommandations',
        'pronostic',
        'evolution_attendue',
        'documents_joints',
        'compte_rendu_pdf',
        'valide',
        'valide_at',
        'valide_par',
        'notes_privees'
    ];

    protected $casts = [
        'date_consultation' => 'date',
        'debut_symptomes' => 'date',
        'prochain_rdv' => 'date',
        'valide_at' => 'datetime',
        'symptomes_declares' => 'array',
        'signes_vitaux' => 'array',
        'examens_complementaires_demandes' => 'array',
        'actes_realises' => 'array',
        'prescriptions' => 'array',
        'examens_prescrits' => 'array',
        'documents_joints' => 'array',
        'ordonnance_delivree' => 'boolean',
        'arret_travail' => 'boolean',
        'certificat_medical' => 'boolean',
        'orientation_specialiste' => 'boolean',
        'hospitalisation' => 'boolean',
        'valide' => 'boolean'
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($consultation) {
            if (!$consultation->numero_consultation) {
                $consultation->numero_consultation = self::generateNumero();
            }
        });

        static::created(function ($consultation) {
            // Mettre à jour le rendez-vous
            if ($consultation->rendez_vous_id) {
                $consultation->rendezVous->update(['statut' => 'termine']);
            }

            // Mettre à jour le dossier médical
            $consultation->updateDossierMedical();
        });
    }

    /**
     * Générer un numéro unique de consultation
     */
    public static function generateNumero(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', now())->count() + 1;
        $number = str_pad($count, 4, '0', STR_PAD_LEFT);

        return "CONS-{$date}-{$number}";
    }

    /**
     * Calculer la durée effective
     */
    public function calculateDuration(): int
    {
        if (!$this->heure_debut || !$this->heure_fin) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->heure_debut);
        $end = \Carbon\Carbon::parse($this->heure_fin);

        return $start->diffInMinutes($end);
    }

    /**
     * Obtenir les constantes vitales formatées
     */
    public function getFormattedVitalSigns(): array
    {
        $vitals = $this->signes_vitaux ?? [];

        return [
            'Tension artérielle' => ($vitals['ta_sys'] ?? '') . '/' . ($vitals['ta_dia'] ?? '') . ' mmHg',
            'Fréquence cardiaque' => ($vitals['fc'] ?? '') . ' bpm',
            'Fréquence respiratoire' => ($vitals['fr'] ?? '') . '/min',
            'Température' => ($vitals['temperature'] ?? '') . '°C',
            'Poids' => ($vitals['poids'] ?? '') . ' kg',
            'Taille' => ($vitals['taille'] ?? '') . ' cm',
            'IMC' => $vitals['imc'] ?? '',
            'SpO2' => ($vitals['spo2'] ?? '') . '%',
        ];
    }

    /**
     * Vérifier si la consultation nécessite un suivi
     */
    public function needsFollowUp(): bool
    {
        return $this->prochain_rdv !== null ||
               $this->orientation_specialiste ||
               $this->hospitalisation ||
               in_array($this->pronostic, ['reserve', 'sombre']);
    }

    /**
     * Mettre à jour le dossier médical du patient
     */
    public function updateDossierMedical(): void
    {
        $dossier = $this->patient->dossierMedical;

        if (!$dossier) {
            return;
        }

        // Mettre à jour les constantes habituelles
        if ($vitals = $this->signes_vitaux) {
            $dossier->update([
                'tension_habituelle_sys' => $vitals['ta_sys'] ?? $dossier->tension_habituelle_sys,
                'tension_habituelle_dia' => $vitals['ta_dia'] ?? $dossier->tension_habituelle_dia,
                'poids_habituel' => $vitals['poids'] ?? $dossier->poids_habituel,
                'taille_cm' => $vitals['taille'] ?? $dossier->taille_cm,
                'imc' => $vitals['imc'] ?? $dossier->imc,
            ]);
        }

        // Mettre à jour la dernière consultation
        $dossier->update([
            'derniere_consultation' => $this->date_consultation,
            'nombre_consultations' => $dossier->nombre_consultations + 1,
        ]);
    }

    // ================== RELATIONS ==================

    /**
     * Rendez-vous associé
     */
    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVous::class, 'rendez_vous_id');
    }

    /**
     * Patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Professionnel de santé
     */
    public function professionnel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professionnel_id');
    }

    /**
     * Structure médicale
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'structure_id');
    }

    /**
     * Validateur
     */
    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * Ordonnances prescrites
     */
    public function ordonnances(): HasMany
    {
        return $this->hasMany(Ordonnance::class, 'consultation_id');
    }

    /**
     * Facture associée
     */
    public function facture(): HasOne
    {
        return $this->hasOne(Facture::class, 'consultation_id');
    }

    /**
     * Devis associé
     */
    public function devis(): HasOne
    {
        return $this->hasOne(Devis::class, 'consultation_id');
    }

    /**
     * Évaluation de la consultation
     */
    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class, 'consultation_id');
    }

    // ================== SCOPES ==================

    /**
     * Scope pour consultations validées
     */
    public function scopeValidated($query)
    {
        return $query->where('valide', true);
    }

    /**
     * Scope pour consultations du jour
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date_consultation', today());
    }

    /**
     * Scope pour consultations avec hospitalisation
     */
    public function scopeWithHospitalization($query)
    {
        return $query->where('hospitalisation', true);
    }

    /**
     * Scope pour consultations par type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour consultations par modalité
     */
    public function scopeOfModality($query, string $modalite)
    {
        return $query->where('modalite', $modalite);
    }

    /**
     * Scope pour consultations avec arrêt de travail
     */
    public function scopeWithSickLeave($query)
    {
        return $query->where('arret_travail', true);
    }

    // ================== MÉTHODES MÉTIER ==================

    /**
     * Générer le compte rendu PDF
     */
    public function generateReport(): string
    {
        // Logique pour générer le PDF du compte rendu
        // Utiliser dompdf ou similaire

        $pdf = \PDF::loadView('pdf.consultation-report', [
            'consultation' => $this,
            'patient' => $this->patient,
            'professionnel' => $this->professionnel,
            'structure' => $this->structure,
        ]);

        $filename = "CR_{$this->numero_consultation}.pdf";
        $path = storage_path("app/public/consultations/{$filename}");

        $pdf->save($path);

        $this->update(['compte_rendu_pdf' => $filename]);

        return $filename;
    }

    /**
     * Créer une ordonnance depuis la consultation
     */
    public function createPrescription(array $medicaments): Ordonnance
    {
        $ordonnance = Ordonnance::create([
            'consultation_id' => $this->id,
            'patient_id' => $this->patient_id,
            'prescripteur_id' => $this->professionnel_id,
            'structure_id' => $this->structure_id,
            'type' => 'simple',
            'nature' => 'initiale',
            'date_prescription' => now(),
            'pathologie' => $this->diagnostic_principal,
            'code_cim10' => $this->code_cim10,
            'valide_jusqu_au' => now()->addMonth(),
        ]);

        foreach ($medicaments as $medicament) {
            $ordonnance->lignes()->create($medicament);
        }

        $this->update(['ordonnance_delivree' => true]);

        return $ordonnance;
    }

    /**
     * Créer un certificat médical
     */
    public function createMedicalCertificate(array $data): string
    {
        $pdf = \PDF::loadView('pdf.certificat-medical', array_merge($data, [
            'consultation' => $this,
            'patient' => $this->patient,
            'professionnel' => $this->professionnel,
        ]));

        $filename = "CERT_{$this->numero_consultation}.pdf";
        $path = storage_path("app/public/certificats/{$filename}");

        $pdf->save($path);

        $this->update(['certificat_medical' => true]);

        return $filename;
    }

    /**
     * Créer un arrêt de travail
     */
    public function createSickLeave(int $days, string $motif): string
    {
        $pdf = Pdf::loadView('pdf.arret-travail', [
            'consultation' => $this,
            'patient' => $this->patient,
            'professionnel' => $this->professionnel,
            'duree' => $days,
            'motif' => $motif,
            'date_debut' => now(),
            'date_fin' => now()->addDays($days),
        ]);

        $filename = "AT_{$this->numero_consultation}.pdf";
        $path = storage_path("app/public/arrets/{$filename}");

        $pdf->save($path);

        $this->update([
            'arret_travail' => true,
            'duree_arret_travail' => $days,
        ]);

        return $filename;
    }

    /**
     * Orienter vers un spécialiste
     */
    public function referToSpecialist(string $specialite, string $motif): void
    {
        $this->update([
            'orientation_specialiste' => true,
            'specialiste_oriente' => $specialite,
            'recommandations' => $this->recommandations . "\n\nOrientation vers {$specialite}: {$motif}",
        ]);

        // Créer une notification pour le patient
        Notification::create([
            'user_id' => $this->patient_id,
            'titre' => 'Orientation vers un spécialiste',
            'message' => "Votre médecin vous oriente vers un {$specialite}. Motif: {$motif}",
            'type' => 'consultation',
            'entite_type' => 'consultation',
            'entite_id' => $this->id,
        ]);
    }

    /**
     * Planifier le prochain rendez-vous
     */
    public function scheduleFollowUp(\DateTimeInterface $date, string $motif): RendezVous
    {
        $rdv = RendezVous::create([
            'patient_id' => $this->patient_id,
            'professionnel_id' => $this->professionnel_id,
            'structure_id' => $this->structure_id,
            'date_heure' => $date,
            'type' => 'controle',
            'modalite' => $this->modalite,
            'motif' => "Contrôle - {$motif}",
            'specialite' => $this->professionnel->specialite,
        ]);

        $this->update(['prochain_rdv' => $date]);

        return $rdv;
    }

    /**
     * Hospitaliser le patient
     */
    public function hospitalizePatient(string $structure = null, string $service = null): void
    {
        $this->update([
            'hospitalisation' => true,
            'structure_hospitalisation' => $structure ?? $this->structure->nom_structure,
        ]);

        // Créer une admission
        // Code pour créer l'admission hospitalière
    }

    /**
     * Obtenir le coût total de la consultation
     */
    public function calculateTotalCost(): float
    {
        $cost = 0;

        // Coût de la consultation elle-même
        if ($this->facture) {
            $cost += $this->facture->montant_final;
        }

        // Coût des examens prescrits
        if ($this->examens_prescrits) {
            foreach ($this->examens_prescrits as $examen) {
                // Récupérer le prix depuis le catalogue
                $acte = ActeMedical::where('code_acte', $examen['code'] ?? '')->first();
                if ($acte) {
                    $cost += $acte->tarif_base * ($examen['quantite'] ?? 1);
                }
            }
        }

        return $cost;
    }

    /**
     * Valider la consultation
     */
    public function validate(User $validator): void
    {
        $this->update([
            'valide' => true,
            'valide_at' => now(),
            'valide_par' => $validator->id,
        ]);
    }
}
