<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facture extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'numero_facture',
        'patient_id',
        'praticien_id',
        'structure_id',
        'devis_id',
        'consultation_id',
        'ordonnance_id',
        'commande_pharmacie_id',
        'type',
        'nature',
        'facture_origine_id',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'montant_remise',
        'montant_majoration',
        'montant_final',
        'part_patient',
        'part_assurance',
        'part_subvention',
        'repartition_payeurs',
        'pec_id',
        'tiers_payant',
        'montant_pec',
        'reste_a_charge',
        'date_facture',
        'date_echeance',
        'delai_paiement',
        'statut_paiement',
        'montant_paye',
        'montant_restant',
        'date_dernier_paiement',
        'nombre_paiements',
        'nombre_relances',
        'derniere_relance',
        'prochaine_relance',
        'facture_pdf',
        'originale_remise',
        'originale_remise_at',
        'comptabilisee',
        'comptabilisee_at',
        'numero_piece_comptable',
        'journal_comptable',
        'notes_internes',
        'mentions_legales'
    ];

    protected $casts = [
        'date_facture' => 'date',
        'date_echeance' => 'date',
        'date_dernier_paiement' => 'date',
        'derniere_relance' => 'date',
        'prochaine_relance' => 'date',
        'originale_remise_at' => 'datetime',
        'comptabilisee_at' => 'datetime',
        'repartition_payeurs' => 'array',
        'tiers_payant' => 'boolean',
        'originale_remise' => 'boolean',
        'comptabilisee' => 'boolean',
        'montant_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'montant_remise' => 'decimal:2',
        'montant_majoration' => 'decimal:2',
        'montant_final' => 'decimal:2',
        'part_patient' => 'decimal:2',
        'part_assurance' => 'decimal:2',
        'part_subvention' => 'decimal:2',
        'montant_pec' => 'decimal:2',
        'reste_a_charge' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_restant' => 'decimal:2'
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($facture) {
            if (!$facture->numero_facture) {
                $facture->numero_facture = self::generateNumero();
            }

            // Calculer la date d'échéance
            if (!$facture->date_echeance) {
                $facture->date_echeance = now()->addDays($facture->delai_paiement ?? 30);
            }

            // Calculer le montant restant
            $facture->montant_restant = $facture->montant_final;
        });

        static::created(function ($facture) {
            // Créer l'écriture comptable
            $facture->createAccountingEntry();

            // Programmer la première relance si nécessaire
            if ($facture->statut_paiement === 'en_attente') {
                $facture->scheduleReminder();
            }
        });

        static::updated(function ($facture) {
            // Mettre à jour le montant restant
            $facture->updateRemainingAmount();

            // Si la facture est payée, créer le reversement
            if ($facture->statut_paiement === 'paye' && !$facture->reversement_cree) {
                $facture->createReversement();
            }
        });
    }

    /**
     * Générer un numéro unique de facture
     */
    public static function generateNumero(): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $lastNumber = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        $number = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);

        return "FAC-{$year}{$month}-{$number}";
    }

    /**
     * Vérifier si la facture est en retard
     */
    public function isOverdue(): bool
    {
        return $this->statut_paiement !== 'paye' &&
               $this->date_echeance < now();
    }

    /**
     * Calculer le nombre de jours de retard
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->date_echeance);
    }

    /**
     * Calculer les pénalités de retard
     */
    public function calculateLateFees(): float
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $daysOverdue = $this->getDaysOverdue();
        $penaltyRate = config('finance.late_fee_rate', 0.01); // 1% par jour de retard
        $maxPenalty = config('finance.max_late_fee', 0.10); // Maximum 10%

        $penalty = min($daysOverdue * $penaltyRate, $maxPenalty);

        return $this->montant_restant * $penalty;
    }

    /**
     * Mettre à jour le montant restant
     */
    public function updateRemainingAmount(): void
    {
        $totalPaid = $this->paiements()
            ->where('statut', 'confirme')
            ->sum('montant');

        $remaining = $this->montant_final - $totalPaid;

        $this->update([
            'montant_paye' => $totalPaid,
            'montant_restant' => max($remaining, 0),
            'statut_paiement' => $this->determinePaymentStatus($totalPaid)
        ]);
    }

    /**
     * Déterminer le statut de paiement
     */
    protected function determinePaymentStatus(float $totalPaid): string
    {
        if ($totalPaid >= $this->montant_final) {
            return 'paye';
        } elseif ($totalPaid > 0) {
            return 'partiel';
        } elseif ($this->isOverdue()) {
            return 'impaye';
        } else {
            return 'en_attente';
        }
    }

    // ================== RELATIONS ==================

    /**
     * Patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Praticien
     */
    public function praticien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'praticien_id');
    }

    /**
     * Structure médicale
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'structure_id');
    }

    /**
     * Devis source
     */
    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class, 'devis_id');
    }

    /**
     * Consultation associée
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }

    /**
     * Ordonnance associée
     */
    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class, 'ordonnance_id');
    }

    /**
     * Commande pharmacie associée
     */
    public function commandePharmacie(): BelongsTo
    {
        return $this->belongsTo(CommandePharmacie::class, 'commande_pharmacie_id');
    }

    /**
     * Prise en charge associée
     */
    public function priseEnCharge(): BelongsTo
    {
        return $this->belongsTo(PriseEnCharge::class, 'pec_id');
    }

    /**
     * Facture d'origine (pour avoirs et rectificatives)
     */
    public function factureOrigine(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'facture_origine_id');
    }

    /**
     * Avoirs et factures rectificatives
     */
    public function corrections(): HasMany
    {
        return $this->hasMany(Facture::class, 'facture_origine_id');
    }

    /**
     * Lignes de facture
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(FactureLigne::class, 'facture_id');
    }

    /**
     * Paiements
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'facture_id');
    }

    /**
     * Remboursements assurance
     */
    public function remboursementsAssurance(): HasMany
    {
        return $this->hasMany(RemboursementAssurance::class, 'facture_id');
    }

    // ================== SCOPES ==================

    /**
     * Scope pour factures payées
     */
    public function scopePaid($query)
    {
        return $query->where('statut_paiement', 'paye');
    }

    /**
     * Scope pour factures impayées
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('statut_paiement', ['en_attente', 'partiel', 'impaye']);
    }

    /**
     * Scope pour factures en retard
     */
    public function scopeOverdue($query)
    {
        return $query->where('statut_paiement', '!=', 'paye')
            ->where('date_echeance', '<', now());
    }

    /**
     * Scope pour factures du mois
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('date_facture', now()->month)
            ->whereYear('date_facture', now()->year);
    }

    /**
     * Scope pour factures comptabilisées
     */
    public function scopeAccounted($query)
    {
        return $query->where('comptabilisee', true);
    }

    // ================== MÉTHODES MÉTIER ==================

    /**
     * Ajouter une ligne de facture
     */
    public function addLine(array $data): FactureLigne
    {
        $ligne = $this->lignes()->create($data);

        $this->recalculateTotals();

        return $ligne;
    }

    /**
     * Recalculer les totaux
     */
    public function recalculateTotals(): void
    {
        $lignes = $this->lignes;

        $montantHT = $lignes->sum('montant_ht');
        $montantTVA = $lignes->sum('montant_tva');
        $montantTTC = $lignes->sum('montant_ttc');
        $montantRemise = $lignes->sum('montant_remise');
        $montantMajoration = $lignes->sum('montant_majoration');
        $montantFinal = $lignes->sum('montant_final');

        $partPatient = $lignes->sum('part_patient');
        $partAssurance = $lignes->sum('part_assurance');
        $partSubvention = $lignes->sum('part_subvention');

        $this->update([
            'montant_ht' => $montantHT,
            'montant_tva' => $montantTVA,
            'montant_ttc' => $montantTTC,
            'montant_remise' => $montantRemise,
            'montant_majoration' => $montantMajoration,
            'montant_final' => $montantFinal,
            'part_patient' => $partPatient,
            'part_assurance' => $partAssurance,
            'part_subvention' => $partSubvention,
            'reste_a_charge' => $partPatient,
            'montant_restant' => $montantFinal - $this->montant_paye,
        ]);
    }

    /**
     * Créer un avoir
     */
    public function createCreditNote(float $amount, string $reason): Facture
    {
        $avoir = Facture::create([
            'numero_facture' => 'AV-' . $this->numero_facture,
            'patient_id' => $this->patient_id,
            'praticien_id' => $this->praticien_id,
            'structure_id' => $this->structure_id,
            'type' => $this->type,
            'nature' => 'avoir',
            'facture_origine_id' => $this->id,
            'montant_ht' => -$amount / 1.18, // Assuming 18% TVA
            'montant_tva' => -$amount * 0.18 / 1.18,
            'montant_ttc' => -$amount,
            'montant_final' => -$amount,
            'date_facture' => now(),
            'date_echeance' => now(),
            'statut_paiement' => 'paye',
            'notes_internes' => "Avoir sur facture {$this->numero_facture}. Motif: {$reason}",
        ]);

        // Créer l'écriture comptable
        $avoir->createAccountingEntry();

        return $avoir;
    }

    /**
     * Enregistrer un paiement
     */
    public function recordPayment(array $data): Paiement
    {
        $data['facture_id'] = $this->id;
        $data['payeur_id'] = $data['payeur_id'] ?? $this->patient_id;
        $data['type_payeur'] = $data['type_payeur'] ?? 'patient';
        $data['numero_paiement'] = Paiement::generateNumero();
        $data['reference_transaction'] = $data['reference_transaction'] ?? uniqid('PAY');
        $data['idempotence_key'] = $data['idempotence_key'] ?? uniqid();
        $data['date_initiation'] = now();

        $paiement = Paiement::create($data);

        // Mettre à jour le statut de la facture
        $this->updateRemainingAmount();

        // Si paiement confirmé, mettre à jour la date du dernier paiement
        if ($paiement->statut === 'confirme') {
            $this->update([
                'date_dernier_paiement' => now(),
                'nombre_paiements' => $this->nombre_paiements + 1,
            ]);
        }

        return $paiement;
    }

    /**
     * Créer une prise en charge
     */
    public function createPriseEnCharge(): ?PriseEnCharge
    {
        if (!$this->patient->hasActiveInsurance()) {
            return null;
        }

        $contrat = $this->patient->contratAssuranceActif;

        $pec = PriseEnCharge::create([
            'numero_pec' => PriseEnCharge::generateNumero(),
            'contrat_id' => $contrat->id,
            'patient_id' => $this->patient_id,
            'assureur_id' => $contrat->assureur_id,
            'prestataire_id' => $this->praticien_id,
            'facture_id' => $this->id,
            'type' => $this->type,
            'nature' => 'initiale',
            'montant_total' => $this->montant_final,
            'montant_couvert' => $this->part_assurance,
            'reste_a_charge' => $this->part_patient,
            'taux_couverture_applique' => $contrat->getCoverageRate($this->type),
            'date_demande' => now(),
            'valide_du' => now(),
            'valide_au' => now()->addMonth(),
        ]);

        $this->update(['pec_id' => $pec->id]);

        return $pec;
    }

    /**
     * Envoyer une relance
     */
    public function sendReminder(): void
    {
        // Envoyer la notification
        Notification::create([
            'user_id' => $this->patient_id,
            'titre' => 'Rappel de paiement',
            'message' => "Votre facture {$this->numero_facture} d'un montant de {$this->montant_restant} FCFA est en attente de paiement.",
            'type' => 'facture',
            'priorite' => $this->isOverdue() ? 'haute' : 'normale',
            'entite_type' => 'facture',
            'entite_id' => $this->id,
            'sms' => true,
            'email' => true,
        ]);

        // Mettre à jour les compteurs
        $this->update([
            'nombre_relances' => $this->nombre_relances + 1,
            'derniere_relance' => now(),
            'prochaine_relance' => now()->addDays(7),
        ]);
    }

    /**
     * Programmer une relance
     */
    public function scheduleReminder(): void
    {
        $delayDays = config('finance.reminder_delay', 7);

        $this->update([
            'prochaine_relance' => $this->date_echeance->subDays($delayDays),
        ]);
    }

    /**
     * Créer l'écriture comptable
     */
    public function createAccountingEntry(): void
    {
        // Logique pour créer l'écriture comptable
        // À implémenter selon le plan comptable
    }

    /**
     * Créer le reversement au praticien
     */
    public function createReversement(): void
    {
        if ($this->reversement_cree) {
            return;
        }

        // Calculer le montant à reverser
        $tauxCommission = $this->structure->commission_plateforme ?? 5;
        $montantBrut = $this->montant_paye;
        $commission = $montantBrut * ($tauxCommission / 100);
        $montantNet = $montantBrut - $commission;

        // Vérifier s'il existe déjà un reversement pour cette période
        $moisAnnee = now()->format('Y-m');
        $reversement = Reversement::firstOrCreate(
            [
                'beneficiaire_id' => $this->praticien_id,
                'type_beneficiaire' => 'praticien',
                'mois_annee' => $moisAnnee,
            ],
            [
                'numero_reversement' => Reversement::generateNumero(),
                'periode_debut' => now()->startOfMonth(),
                'periode_fin' => now()->endOfMonth(),
                'montant_brut' => 0,
                'commission_plateforme' => 0,
                'taux_commission' => $tauxCommission,
                'montant_net' => 0,
                'date_calcul' => now(),
                'date_paiement_prevu' => now()->endOfMonth()->addDays(5),
                'statut' => 'calcule',
            ]
        );

        // Ajouter le montant de cette facture
        $reversement->update([
            'montant_brut' => $reversement->montant_brut + $montantBrut,
            'commission_plateforme' => $reversement->commission_plateforme + $commission,
            'montant_net' => $reversement->montant_net + $montantNet,
            'nombre_consultations' => $reversement->nombre_consultations + 1,
        ]);

        $this->update(['reversement_cree' => true]);
    }

    /**
     * Générer le PDF de la facture
     */
    public function generatePDF(): string
    {
        $pdf = \PDF::loadView('pdf.facture', [
            'facture' => $this,
            'patient' => $this->patient,
            'praticien' => $this->praticien,
            'structure' => $this->structure,
            'lignes' => $this->lignes,
        ]);

        $filename = "{$this->numero_facture}.pdf";
        $path = storage_path("app/public/factures/{$filename}");

        $pdf->save($path);

        $this->update(['facture_pdf' => $filename]);

        return $filename;
    }

    /**
     * Marquer comme comptabilisée
     */
    public function markAsAccounted(string $numeroPiece = null, string $journal = null): void
    {
        $this->update([
            'comptabilisee' => true,
            'comptabilisee_at' => now(),
            'numero_piece_comptable' => $numeroPiece ?? $this->numero_facture,
            'journal_comptable' => $journal ?? 'VTE', // Journal des ventes
        ]);
    }
}
