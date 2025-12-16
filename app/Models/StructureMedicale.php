<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StructureMedicale extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'structures_medicales';

    protected $fillable = [
        'code_structure',
        'nom_structure',
        'type_structure',
        'numero_agrement',
        'numero_fiscal',
        'registre_commerce',
        'adresse_rue',
        'adresse_quartier',
        'adresse_ville',
        'adresse_pays',
        'latitude',
        'longitude',
        'telephone_principal',
        'telephone_secondaire',
        'email',
        'site_web',
        'horaires_ouverture',
        'urgences_24h',
        'garde_weekend',
        'responsable_id',
        'services_disponibles',
        'equipements',
        'nombre_lits',
        'nombre_salles',
        'parking_disponible',
        'accessible_handicapes',
        'assurances_acceptees',
        'tiers_payant',
        'categorie_tarif',
        'taux_majoration',
        'statut',
        'verified',
        'verified_at',
        'verified_by',
        'logo',
        'photo_facade',
        'galerie_photos',
        'document_agrement',
        'note_moyenne',
        'nombre_evaluations',
        'nombre_consultations',
        'compte_bancaire',
        'code_banque',
        'iban',
        'commission_plateforme'
    ];

    protected $casts = [
        'horaires_ouverture' => 'array',
        'services_disponibles' => 'array',
        'equipements' => 'array',
        'assurances_acceptees' => 'array',
        'galerie_photos' => 'array',
        'urgences_24h' => 'boolean',
        'garde_weekend' => 'boolean',
        'parking_disponible' => 'boolean',
        'accessible_handicapes' => 'boolean',
        'tiers_payant' => 'boolean',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'taux_majoration' => 'decimal:2',
        'note_moyenne' => 'decimal:2',
        'commission_plateforme' => 'decimal:2'
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($structure) {
            if (!$structure->code_structure) {
                $structure->code_structure = self::generateCode($structure);
            }
        });
    }

    /**
     * Générer un code unique pour la structure
     */
    public static function generateCode($structure): string
    {
        $prefix = match($structure->type_structure) {
            'cabinet' => 'CAB',
            'clinique' => 'CLN',
            'hopital' => 'HOP',
            'pharmacie' => 'PHR',
            'laboratoire' => 'LAB',
            'centre_imagerie' => 'IMG',
            'centre_specialise' => 'CSP',
            default => 'STR'
        };

        $year = date('Y');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$random}";
    }

    /**
     * Vérifier si la structure est ouverte maintenant
     */
    public function isOpenNow(): bool
    {
        if ($this->urgences_24h) {
            return true;
        }

        $dayOfWeek = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        $horaires = $this->horaires_ouverture[$dayOfWeek] ?? null;

        if (!$horaires || $horaires === 'closed') {
            return false;
        }

        [$opening, $closing] = explode('-', $horaires);

        return $currentTime >= $opening && $currentTime <= $closing;
    }

    /**
     * Obtenir les heures d'ouverture du jour
     */
    public function getTodayHours(): ?string
    {
        if ($this->urgences_24h) {
            return '24h/24';
        }

        $dayOfWeek = strtolower(now()->format('l'));
        return $this->horaires_ouverture[$dayOfWeek] ?? 'Fermé';
    }

    /**
     * Calculer la distance depuis des coordonnées
     */
    public function distanceFrom($latitude, $longitude): float
    {
        if (!$this->latitude || !$this->longitude) {
            return PHP_FLOAT_MAX;
        }

        $earthRadius = 6371; // km

        $latDiff = deg2rad($latitude - $this->latitude);
        $lonDiff = deg2rad($longitude - $this->longitude);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
            sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Vérifier si une assurance est acceptée
     */
    public function acceptsInsurance(string $insuranceId): bool
    {
        if (!$this->tiers_payant) {
            return false;
        }

        $acceptedInsurances = $this->assurances_acceptees ?? [];
        return in_array($insuranceId, $acceptedInsurances);
    }

    /**
     * Obtenir le tarif avec majoration
     */
    public function calculatePrice(float $basePrice): float
    {
        $majoration = $this->taux_majoration ?? 0;
        return $basePrice * (1 + $majoration / 100);
    }

    // ================== RELATIONS ==================

    /**
     * Responsable de la structure
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Utilisateur qui a vérifié la structure
     */
    public function verificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Personnel de la structure
     */
    public function personnel(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_structure', 'structure_id', 'user_id')
            ->withPivot(['role', 'actif', 'date_debut', 'date_fin', 'pourcentage_honoraires'])
            ->withTimestamps();
    }

    /**
     * Praticiens actifs
     */
    public function praticiensActifs(): BelongsToMany
    {
        return $this->personnel()
            ->wherePivot('actif', true)
            ->wherePivot('role', 'praticien');
    }

    /**
     * Actes médicaux de la structure
     */
    public function actesMedicaux(): HasMany
    {
        return $this->hasMany(ActeMedical::class, 'structure_id');
    }

    /**
     * Produits pharmaceutiques (pour pharmacies)
     */
    public function produitsPharmaceutiques(): HasMany
    {
        return $this->hasMany(StockPharmacie::class, 'pharmacie_id');
    }

    /**
     * Rendez-vous dans la structure
     */
    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class, 'structure_id');
    }

    /**
     * Consultations dans la structure
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'structure_id');
    }

    /**
     * Ordonnances émises
     */
    public function ordonnances(): HasMany
    {
        return $this->hasMany(Ordonnance::class, 'structure_id');
    }

    /**
     * Ordonnances dispensées (pour pharmacies)
     */
    public function ordonnancesDispensees(): HasMany
    {
        return $this->hasMany(Ordonnance::class, 'pharmacie_dispensatrice_id');
    }

    /**
     * Commandes pharmacie
     */
    public function commandesPharmacie(): HasMany
    {
        return $this->hasMany(CommandePharmacie::class, 'pharmacie_id');
    }

    /**
     * Factures de la structure
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class, 'structure_id');
    }

    /**
     * Grilles tarifaires
     */
    public function grillesTarifaires(): HasMany
    {
        return $this->hasMany(GrilleTarifaire::class, 'structure_id');
    }

    /**
     * Évaluations reçues
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'evalue_id')
            ->where('type_evalue', 'structure');
    }

    /**
     * Litiges concernant la structure
     */
    public function litiges(): HasMany
    {
        return $this->hasMany(Litige::class, 'structure_concernee_id');
    }

    // ================== SCOPES ==================

    /**
     * Scope pour structures actives
     */
    public function scopeActive($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Scope pour structures vérifiées
     */
    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    /**
     * Scope par type de structure
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type_structure', $type);
    }

    /**
     * Scope pour pharmacies
     */
    public function scopePharmacies($query)
    {
        return $query->where('type_structure', 'pharmacie');
    }

    /**
     * Scope pour structures avec urgences
     */
    public function scopeWithEmergency($query)
    {
        return $query->where('urgences_24h', true);
    }

    /**
     * Scope pour recherche par proximité
     */
    public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        $haversine = "(6371 * acos(cos(radians($latitude))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians($longitude))
            + sin(radians($latitude))
            * sin(radians(latitude))))";

        return $query->select('*')
            ->selectRaw("{$haversine} AS distance")
            ->whereRaw("{$haversine} < ?", [$radius])
            ->orderBy('distance');
    }

    /**
     * Scope pour recherche par ville
     */
    public function scopeInCity($query, string $city)
    {
        return $query->where('adresse_ville', 'LIKE', "%{$city}%");
    }

    /**
     * Scope pour structures acceptant le tiers payant
     */
    public function scopeWithThirdPartyPayment($query)
    {
        return $query->where('tiers_payant', true);
    }

    /**
     * Scope pour structures ouvertes maintenant
     */
    public function scopeOpenNow($query)
    {
        // Cette méthode nécessite un traitement plus complexe
        // Idéalement, on devrait filtrer au niveau PHP après récupération
        return $query->where(function ($q) {
            $q->where('urgences_24h', true)
              ->orWhereNotNull('horaires_ouverture');
        });
    }

    // ================== MÉTHODES MÉTIER ==================

    /**
     * Obtenir les statistiques de la structure
     */
    public function getStats(): array
    {
        return [
            'total_consultations' => $this->consultations()->count(),
            'consultations_mois' => $this->consultations()
                ->whereMonth('date_consultation', now()->month)
                ->count(),
            'praticiens_actifs' => $this->praticiensActifs()->count(),
            'note_moyenne' => $this->note_moyenne,
            'nombre_evaluations' => $this->nombre_evaluations,
            'taux_satisfaction' => $this->evaluations()
                ->where('recommande', true)
                ->count() / max($this->nombre_evaluations, 1) * 100,
            'chiffre_affaires_mois' => $this->factures()
                ->whereMonth('date_facture', now()->month)
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
        ];
    }

    /**
     * Obtenir les disponibilités pour un jour donné
     */
    public function getAvailabilities(\DateTimeInterface $date): array
    {
        $dayOfWeek = strtolower($date->format('l'));
        $horaires = $this->horaires_ouverture[$dayOfWeek] ?? null;

        if (!$horaires || $horaires === 'closed') {
            return [];
        }

        // Récupérer les rendez-vous existants
        $appointments = $this->rendezVous()
            ->whereDate('date_heure', $date)
            ->whereIn('statut', ['confirme', 'en_attente'])
            ->pluck('date_heure')
            ->map(fn($dt) => $dt->format('H:i'))
            ->toArray();

        // Générer les créneaux disponibles
        [$opening, $closing] = explode('-', $horaires);
        $slots = [];
        $current = \DateTime::createFromFormat('H:i', $opening);
        $end = \DateTime::createFromFormat('H:i', $closing);

        while ($current < $end) {
            $timeSlot = $current->format('H:i');
            if (!in_array($timeSlot, $appointments)) {
                $slots[] = $timeSlot;
            }
            $current->modify('+30 minutes'); // Créneaux de 30 minutes
        }

        return $slots;
    }

    /**
     * Mettre à jour la note moyenne
     */
    public function updateAverageRating(): void
    {
        $stats = $this->evaluations()
            ->selectRaw('AVG(note_globale) as moyenne, COUNT(*) as total')
            ->first();

        $this->update([
            'note_moyenne' => $stats->moyenne ?? 0,
            'nombre_evaluations' => $stats->total ?? 0,
        ]);
    }

    /**
     * Vérifier la capacité d'accueil
     */
    public function hasCapacity(): bool
    {
        if ($this->type_structure === 'hopital' || $this->type_structure === 'clinique') {
            $occupiedBeds = $this->consultations()
                ->where('hospitalisation', true)
                ->whereNull('date_sortie')
                ->count();

            return $occupiedBeds < $this->nombre_lits;
        }

        return true;
    }

    /**
     * Obtenir les praticiens disponibles
     */
    public function getAvailablePractitioners(\DateTimeInterface $dateTime)
    {
        return $this->praticiensActifs()
            ->whereDoesntHave('rendezVousProfessionnel', function ($query) use ($dateTime) {
                $query->where('date_heure', $dateTime)
                    ->whereIn('statut', ['confirme', 'en_cours']);
            })
            ->get();
    }

    /**
     * Calculer les revenus sur une période
     */
    public function calculateRevenue(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $factures = $this->factures()
            ->whereBetween('date_facture', [$startDate, $endDate])
            ->where('statut_paiement', 'paye')
            ->get();

        $total = $factures->sum('montant_final');
        $commission = $total * ($this->commission_plateforme / 100);
        $net = $total - $commission;

        return [
            'brut' => $total,
            'commission' => $commission,
            'net' => $net,
            'nombre_factures' => $factures->count(),
        ];
    }
}
