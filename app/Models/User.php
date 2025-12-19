<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'telephone',
        'email',
        'password',
        'adresse_rue',
        'adresse_quartier',
        'adresse_ville',
        'adresse_pays',
        'latitude',
        'longitude',
        'specialite',
        'numero_ordre',
        'certification_document',
        'certification_verified',
        'certification_verified_at',
        'certification_verified_by',
        'photo_profil',
        'piece_identite',
        'piece_identite_numero',
        'piece_identite_type',
        'statut_compte',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'langue_preferee',
        'notifications_sms',
        'notifications_email',
        'notifications_push',
        'login_count',
        'last_login_at',
        'last_login_ip',
        'note_moyenne',
        'nombre_evaluations',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'api_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'certification_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'date_naissance' => 'date',
        'certification_verified' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'two_factor_secret' => 'encrypted',
        'two_factor_recovery_codes' => 'encrypted',
        'notifications_sms' => 'boolean',
        'notifications_email' => 'boolean',
        'notifications_push' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'note_moyenne' => 'decimal:2',
    ];

    /**
     * Virtual attributes.
     */
    protected $appends = ['name'];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->matricule) {
                $user->matricule = self::generateMatricule($user);
            }
        });
    }

    /**
     * Générer un matricule unique
     */
    public static function generateMatricule($user): string
    {
        $prefix = 'LBK';
        $year = date('Y');
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$random}";
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Get the user's age
     */
    public function getAgeAttribute(): int
    {
        return $this->date_naissance ? $this->date_naissance->age : 0;
    }

    /**
     * Adapter l'attribut virtuel "name" sur prenom/nom.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(($this->prenom ?? '') . ' ' . ($this->nom ?? '')),
            set: function ($value) {
                $parts = preg_split('/\s+/', trim((string) $value), 2);
                return [
                    'prenom' => $parts[0] ?? '',
                    'nom' => $parts[1] ?? '',
                ];
            }
        );
    }

    /**
     * Check if user is a professional
     */
    public function isProfessional(): bool
    {
        return in_array($this->getRoleNames()->first(), [
            'medecin',
            'pharmacien',
            'infirmier',
            'sage-femme',
            'dentiste',
            'biologiste'
        ]);
    }

    /**
     * Check if user is a patient
     */
    public function isPatient(): bool
    {
        return $this->hasRole('patient');
    }

    /**
     * Check if user is verified professional
     */
    public function isVerifiedProfessional(): bool
    {
        return $this->isProfessional() && $this->certification_verified;
    }

    /**
     * Check if account is active
     */
    public function isActive(): bool
    {
        return $this->statut_compte === 'actif';
    }

    /**
     * Calculate distance from coordinates
     */
    public function distanceFrom($latitude, $longitude): float
    {
        if (!$this->latitude || !$this->longitude) {
            return PHP_FLOAT_MAX;
        }

        $earthRadius = 6371; // Rayon de la Terre en kilomètres

        $latDiff = deg2rad($latitude - $this->latitude);
        $lonDiff = deg2rad($longitude - $this->longitude);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
            sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // ================== RELATIONS ==================

    /**
     * Structures médicales associées
     */
    public function structures(): BelongsToMany
    {
        return $this->belongsToMany(StructureMedicale::class, 'user_structure', 'user_id', 'structure_id')
            ->withPivot(['role', 'actif', 'date_debut', 'date_fin', 'pourcentage_honoraires'])
            ->withTimestamps();
    }

    /**
     * Structure principale (pour les professionnels)
     */
    public function structurePrincipale(): HasOne
    {
        return $this->hasOne(StructureMedicale::class, 'responsable_id');
    }

    /**
     * Dossier médical (pour les patients)
     */
    public function dossierMedical(): HasOne
    {
        return $this->hasOne(DossierMedical::class, 'patient_id');
    }

    /**
     * Rendez-vous en tant que patient
     */
    public function rendezVousPatient(): HasMany
    {
        return $this->hasMany(RendezVous::class, 'patient_id');
    }

    /**
     * Rendez-vous en tant que professionnel
     */
    public function rendezVousProfessionnel(): HasMany
    {
        return $this->hasMany(RendezVous::class, 'professionnel_id');
    }

    /**
     * Consultations en tant que patient
     */
    public function consultationsPatient(): HasMany
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }

    /**
     * Consultations en tant que professionnel
     */
    public function consultationsProfessionnel(): HasMany
    {
        return $this->hasMany(Consultation::class, 'professionnel_id');
    }

    /**
     * Ordonnances en tant que patient
     */
    public function ordonnancesPatient(): HasMany
    {
        return $this->hasMany(Ordonnance::class, 'patient_id');
    }

    /**
     * Ordonnances en tant que prescripteur
     */
    public function ordonnancesPrescripteur(): HasMany
    {
        return $this->hasMany(Ordonnance::class, 'prescripteur_id');
    }

    /**
     * Contrats d'assurance
     */
    public function contratsAssurance(): HasMany
    {
        return $this->hasMany(ContratAssurance::class, 'patient_id');
    }

    /**
     * Contrat d'assurance actif
     */
    public function contratAssuranceActif(): HasOne
    {
        return $this->hasOne(ContratAssurance::class, 'patient_id')
            ->where('statut', 'actif')
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now());
    }

    /**
     * Factures en tant que patient
     */
    public function facturesPatient(): HasMany
    {
        return $this->hasMany(Facture::class, 'patient_id');
    }

    /**
     * Factures en tant que praticien
     */
    public function facturesPraticien(): HasMany
    {
        return $this->hasMany(Facture::class, 'praticien_id');
    }

    /**
     * Paiements effectués
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'payeur_id');
    }

    /**
     * Reversements reçus
     */
    public function reversements(): HasMany
    {
        return $this->hasMany(Reversement::class, 'beneficiaire_id')
            ->where('type_beneficiaire', 'praticien');
    }

    /**
     * Commandes pharmacie
     */
    public function commandesPharmacie(): HasMany
    {
        return $this->hasMany(CommandePharmacie::class, 'patient_id');
    }

    /**
     * Notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Litiges déclarés
     */
    public function litiges(): HasMany
    {
        return $this->hasMany(Litige::class, 'declarant_id');
    }

    /**
     * Évaluations données
     */
    public function evaluationsDonnees(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'evaluateur_id');
    }

    /**
     * Évaluations reçues
     */
    public function evaluationsRecues(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'evalue_id')
            ->where('type_evalue', 'praticien');
    }

    /**
     * Logs d'audit
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ================== MÉTHODES MÉTIER ==================

    /**
     * Vérifier si le patient a une assurance active
     */
    public function hasActiveInsurance(): bool
    {
        return $this->contratAssuranceActif()->exists();
    }

    /**
     * Obtenir le taux de couverture pour un type d'acte
     */
    public function getCoveragRate(string $type): float
    {
        $contrat = $this->contratAssuranceActif;

        if (!$contrat) {
            return 0;
        }

        return match($type) {
            'consultation' => $contrat->taux_couverture_consultation,
            'pharmacie' => $contrat->taux_couverture_pharmacie,
            'hospitalisation' => $contrat->taux_couverture_hospitalisation,
            'analyse' => $contrat->taux_couverture_analyse,
            'imagerie' => $contrat->taux_couverture_imagerie,
            'dentaire' => $contrat->taux_couverture_dentaire,
            'optique' => $contrat->taux_couverture_optique,
            default => 0,
        };
    }

    /**
     * Calculer le reste à charge pour un montant donné
     */
    public function calculateOutOfPocket(float $amount, string $type): float
    {
        $coverageRate = $this->getCoveragRate($type);
        return $amount * (1 - $coverageRate / 100);
    }

    /**
     * Obtenir les prochains rendez-vous
     */
    public function getUpcomingAppointments(int $limit = 5)
    {
        $query = $this->isPatient()
            ? $this->rendezVousPatient()
            : $this->rendezVousProfessionnel();

        return $query->where('date_heure', '>=', now())
            ->whereIn('statut', ['en_attente', 'confirme'])
            ->orderBy('date_heure')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les statistiques du praticien
     */
    public function getPractitionerStats(): array
    {
        if (!$this->isProfessional()) {
            return [];
        }

        return [
            'total_consultations' => $this->consultationsProfessionnel()->count(),
            'consultations_mois' => $this->consultationsProfessionnel()
                ->whereMonth('date_consultation', now()->month)
                ->count(),
            'patients_mois' => $this->consultationsProfessionnel()
                ->whereMonth('date_consultation', now()->month)
                ->distinct('patient_id')
                ->count('patient_id'),
            'note_moyenne' => $this->note_moyenne,
            'nombre_evaluations' => $this->nombre_evaluations,
            'taux_satisfaction' => $this->evaluationsRecues()
                ->where('recommande', true)
                ->count() / max($this->nombre_evaluations, 1) * 100,
        ];
    }

    /**
     * Obtenir les statistiques du patient
     */
    public function getPatientStats(): array
    {
        if (!$this->isPatient()) {
            return [];
        }

        return [
            'total_consultations' => $this->consultationsPatient()->count(),
            'total_ordonnances' => $this->ordonnancesPatient()->count(),
            'total_depenses' => $this->paiements()
                ->where('statut', 'confirme')
                ->sum('montant'),
            'economies_assurance' => $this->facturesPatient()
                ->sum('montant_pec'),
            'rendez_vous_manques' => $this->rendezVousPatient()
                ->where('statut', 'no_show')
                ->count(),
        ];
    }

    /**
     * Mettre à jour les coordonnées GPS
     */
    public function updateCoordinates(float $latitude, float $longitude): void
    {
        $this->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    /**
     * Enregistrer une connexion
     */
    public function recordLogin(string $ip): void
    {
        $this->update([
            'login_count' => $this->login_count + 1,
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Mettre à jour la note moyenne
     */
    public function updateAverageRating(): void
    {
        $stats = $this->evaluationsRecues()
            ->selectRaw('AVG(note_globale) as moyenne, COUNT(*) as total')
            ->first();

        $this->update([
            'note_moyenne' => $stats->moyenne ?? 0,
            'nombre_evaluations' => $stats->total ?? 0,
        ]);
    }

    /**
     * Scope pour les professionnels actifs
     */
    public function scopeActiveProfessionals($query)
    {
        return $query->where('statut_compte', 'actif')
            ->where('certification_verified', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', [
                    'medecin',
                    'pharmacien',
                    'infirmier',
                    'sage-femme',
                    'dentiste',
                    'biologiste'
                ]);
            });
    }

    /**
     * Scope pour recherche par proximité
     */
    public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        $lat = (float) $latitude;
        $lon = (float) $longitude;
        $rad = (float) $radius;
        $haversine = "(6371 * acos(cos(radians($latitude))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians($longitude))
            + sin(radians($latitude))
            * sin(radians(latitude))))";

        return $query->select('*')
            ->selectRaw("{$haversine} AS distance")
            ->whereRaw("{$haversine} < ?", [$rad])
            ->orderBy('distance');
    }

    /**
     * Scope pour recherche par spécialité
     */
    public function scopeBySpecialty($query, string $specialty)
    {
        return $query->where('specialite', $specialty);
    }

    /**
     * Scope pour recherche par ville
     */
    public function scopeInCity($query, string $city)
    {
        return $query->where('adresse_ville', 'LIKE', "%{$city}%");
    }
}
