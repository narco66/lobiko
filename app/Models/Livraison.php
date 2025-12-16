<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Incident;


class Livraison extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'livraisons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'numero_livraison',
        'commande_id',
        'livreur_id',
        'pharmacie_id',
        'patient_id',
        'type_livraison',
        'priorite',
        'statut',

        // Adresse de livraison
        'adresse_depart',
        'latitude_depart',
        'longitude_depart',
        'adresse_livraison',
        'latitude_livraison',
        'longitude_livraison',
        'instructions_livraison',
        'contact_livraison',
        'telephone_contact',

        // Dates et heures
        'date_programmee',
        'heure_debut_souhaitee',
        'heure_fin_souhaitee',
        'date_prise_en_charge',
        'date_depart',
        'date_arrivee',
        'date_livraison_effective',

        // Informations de livraison
        'distance_km',
        'duree_estimee_minutes',
        'duree_reelle_minutes',
        'frais_livraison',
        'pourboire',
        'mode_transport',
        'vehicule_info',

        // Validation et preuve
        'code_validation',
        'signature_client',
        'photo_livraison',
        'photo_colis',
        'commentaire_livreur',
        'commentaire_client',
        'note_satisfaction',

        // Gestion des problèmes
        'probleme_signale',
        'type_probleme',
        'description_probleme',
        'resolution_probleme',

        // Tracking temps réel
        'tracking_actif',
        'derniere_position_lat',
        'derniere_position_lng',
        'derniere_mise_a_jour_position',
        'historique_positions',

        // Métadonnées
        'temperature_transport',
        'conditions_speciales',
        'assurance_livraison',
        'valeur_declaree',
        'poids_kg',
        'dimensions',
        'fragile',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_programmee' => 'datetime',
        'heure_debut_souhaitee' => 'datetime',
        'heure_fin_souhaitee' => 'datetime',
        'date_prise_en_charge' => 'datetime',
        'date_depart' => 'datetime',
        'date_arrivee' => 'datetime',
        'date_livraison_effective' => 'datetime',
        'derniere_mise_a_jour_position' => 'datetime',

        'latitude_depart' => 'decimal:8',
        'longitude_depart' => 'decimal:8',
        'latitude_livraison' => 'decimal:8',
        'longitude_livraison' => 'decimal:8',
        'derniere_position_lat' => 'decimal:8',
        'derniere_position_lng' => 'decimal:8',

        'distance_km' => 'decimal:2',
        'duree_estimee_minutes' => 'integer',
        'duree_reelle_minutes' => 'integer',
        'frais_livraison' => 'decimal:2',
        'pourboire' => 'decimal:2',
        'note_satisfaction' => 'integer',
        'poids_kg' => 'decimal:2',
        'valeur_declaree' => 'decimal:2',

        'tracking_actif' => 'boolean',
        'probleme_signale' => 'boolean',
        'fragile' => 'boolean',
        'assurance_livraison' => 'boolean',

        'historique_positions' => 'array',
        'dimensions' => 'array',
        'conditions_speciales' => 'array',
        'metadata' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'statut' => 'en_preparation',
        'type_livraison' => 'standard',
        'priorite' => 'normale',
        'tracking_actif' => false,
        'probleme_signale' => false,
        'fragile' => false,
        'assurance_livraison' => false,
        'frais_livraison' => 0,
        'pourboire' => 0,
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($livraison) {
            if (empty($livraison->numero_livraison)) {
                $livraison->numero_livraison = self::generateNumeroLivraison();
            }

            if (empty($livraison->code_validation)) {
                $livraison->code_validation = self::generateCodeValidation();
            }

            // Calculer la distance si les coordonnées sont fournies
            if ($livraison->hasCoordinates()) {
                $livraison->distance_km = $livraison->calculerDistance();
                $livraison->duree_estimee_minutes = $livraison->estimerDuree();
            }
        });

        static::updating(function ($livraison) {
            // Mettre à jour l'historique des positions si tracking actif
            if ($livraison->tracking_actif && $livraison->isDirty(['derniere_position_lat', 'derniere_position_lng'])) {
                $livraison->ajouterPositionHistorique();
            }

            // Calculer la durée réelle si livraison terminée
            if ($livraison->isDirty('statut') && $livraison->statut === 'livree') {
                $livraison->calculerDureeReelle();
            }
        });
    }

    // =====================================
    // RELATIONS
    // =====================================

    /**
     * Commande associée
     */
    public function commande(): BelongsTo
    {
        return $this->belongsTo(CommandePharmacie::class, 'commande_id');
    }

    /**
     * Livreur
     */
    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    /**
     * Pharmacie
     */
    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'pharmacie_id');
    }

    /**
     * Patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Notifications liées
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'entity_id')
                    ->where('entity_type', 'livraison');
    }

    /**
     * Évaluations
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'livraison_id');
    }

    /**
     * Incidents
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'livraison_id');
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Livraisons en cours
     */
    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', ['affectee', 'en_route', 'arrivee']);
    }

    /**
     * Livraisons du jour
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_programmee', today());
    }

    /**
     * Livraisons en retard
     */
    public function scopeEnRetard($query)
    {
        return $query->where('statut', '!=', 'livree')
                    ->where('statut', '!=', 'annulee')
                    ->where('date_programmee', '<', now());
    }

    /**
     * Livraisons par livreur
     */
    public function scopeLivreur($query, $livreurId)
    {
        return $query->where('livreur_id', $livreurId);
    }

    /**
     * Livraisons par zone
     */
    public function scopeDansZone($query, $latitude, $longitude, $rayon = 10)
    {
        // Formule Haversine pour calculer la distance
        $haversine = "(6371 * acos(cos(radians($latitude))
                    * cos(radians(latitude_livraison))
                    * cos(radians(longitude_livraison) - radians($longitude))
                    + sin(radians($latitude))
                    * sin(radians(latitude_livraison))))";

        return $query->selectRaw("*, $haversine AS distance")
                    ->having('distance', '<=', $rayon)
                    ->orderBy('distance');
    }

    /**
     * Livraisons urgentes
     */
    public function scopeUrgentes($query)
    {
        return $query->where('priorite', 'urgente')
                    ->orWhere('type_livraison', 'express');
    }

    // =====================================
    // MÉTHODES DE GESTION DU STATUT
    // =====================================

    /**
     * Affecter un livreur
     */
    public function affecterLivreur(User $livreur): void
    {
        if (!$livreur->hasRole('livreur')) {
            throw new \Exception("L'utilisateur n'est pas un livreur");
        }

        $this->livreur_id = $livreur->id;
        $this->statut = 'affectee';
        $this->date_prise_en_charge = now();
        $this->save();

        // Notifier le livreur
        $this->notifierLivreur('Nouvelle livraison',
            "Une nouvelle livraison vous a été affectée : {$this->numero_livraison}");

        // Notifier le patient
        $this->notifierPatient('Livreur affecté',
            "Un livreur a été affecté à votre livraison");
    }

    /**
     * Démarrer la livraison
     */
    public function demarrerLivraison(): void
    {
        if ($this->statut !== 'affectee') {
            throw new \Exception("La livraison doit être affectée pour être démarrée");
        }

        $this->statut = 'en_route';
        $this->date_depart = now();
        $this->tracking_actif = true;
        $this->save();

        $this->notifierPatient('Livraison en route',
            "Votre livraison {$this->numero_livraison} est en route");
    }

    /**
     * Marquer comme arrivé
     */
    public function marquerArrivee(): void
    {
        if ($this->statut !== 'en_route') {
            throw new \Exception("La livraison doit être en route");
        }

        $this->statut = 'arrivee';
        $this->date_arrivee = now();
        $this->save();

        $this->notifierPatient('Livreur arrivé',
            "Le livreur est arrivé à votre adresse. Code de validation : {$this->code_validation}");
    }

    /**
     * Confirmer la livraison
     */
    public function confirmerLivraison(array $preuves = []): void
    {
        if (!in_array($this->statut, ['arrivee', 'en_route'])) {
            throw new \Exception("Statut invalide pour confirmer la livraison");
        }

        $this->statut = 'livree';
        $this->date_livraison_effective = now();
        $this->tracking_actif = false;

        if (isset($preuves['signature'])) {
            $this->signature_client = $preuves['signature'];
        }

        if (isset($preuves['photo'])) {
            $this->photo_livraison = $preuves['photo'];
        }

        if (isset($preuves['code']) && $preuves['code'] !== $this->code_validation) {
            throw new \Exception("Code de validation incorrect");
        }

        $this->save();

        // Mettre à jour la commande
        if ($this->commande) {
            $this->commande->marquerLivree($preuves);
        }

        $this->notifierPatient('Livraison confirmée',
            "Votre livraison {$this->numero_livraison} a été confirmée");
    }

    /**
     * Annuler la livraison
     */
    public function annuler(string $motif, string $initiateur = 'client'): void
    {
        if (in_array($this->statut, ['livree', 'annulee'])) {
            throw new \Exception("Cette livraison ne peut plus être annulée");
        }

        $ancienStatut = $this->statut;
        $this->statut = 'annulee';
        $this->tracking_actif = false;

        $metadata = $this->metadata ?? [];
        $metadata['annulation'] = [
            'motif' => $motif,
            'initiateur' => $initiateur,
            'date' => now()->toDateTimeString(),
            'ancien_statut' => $ancienStatut
        ];
        $this->metadata = $metadata;

        $this->save();

        // Notifier les parties concernées
        if ($initiateur === 'client') {
            $this->notifierLivreur('Livraison annulée',
                "La livraison {$this->numero_livraison} a été annulée par le client");
        } else {
            $this->notifierPatient('Livraison annulée',
                "Votre livraison {$this->numero_livraison} a été annulée");
        }
    }

    /**
     * Signaler un problème
     */
    public function signalerProbleme(string $type, string $description): void
    {
        $this->probleme_signale = true;
        $this->type_probleme = $type;
        $this->description_probleme = $description;
        $this->save();

        // Créer un incident
        Incident::create([
            'livraison_id' => $this->id,
            'type' => $type,
            'description' => $description,
            'statut' => 'ouvert',
            'priorite' => $this->determinerPrioriteIncident($type)
        ]);

        // Notifier le support
        $this->notifierSupport("Problème signalé sur la livraison {$this->numero_livraison}");
    }

    /**
     * Résoudre un problème
     */
    public function resoudreProbleme(string $resolution): void
    {
        $this->resolution_probleme = $resolution;
        $this->save();

        // Mettre à jour l'incident
        $incident = $this->incidents()->where('statut', 'ouvert')->latest()->first();
        if ($incident) {
            $incident->resoudre($resolution);
        }
    }

    // =====================================
    // MÉTHODES DE TRACKING
    // =====================================

    /**
     * Activer le tracking
     */
    public function activerTracking(): void
    {
        $this->tracking_actif = true;
        $this->save();
    }

    /**
     * Désactiver le tracking
     */
    public function desactiverTracking(): void
    {
        $this->tracking_actif = false;
        $this->save();
    }

    /**
     * Mettre à jour la position
     */
    public function mettreAJourPosition(float $latitude, float $longitude): void
    {
        if (!$this->tracking_actif) {
            return;
        }

        $this->derniere_position_lat = $latitude;
        $this->derniere_position_lng = $longitude;
        $this->derniere_mise_a_jour_position = now();
        $this->save();

        // Vérifier si proche de la destination
        $distanceRestante = $this->calculerDistanceRestante();
        if ($distanceRestante < 0.1) { // Moins de 100m
            $this->marquerArrivee();
        }
    }

    /**
     * Ajouter position à l'historique
     */
    protected function ajouterPositionHistorique(): void
    {
        $historique = $this->historique_positions ?? [];

        $historique[] = [
            'lat' => $this->derniere_position_lat,
            'lng' => $this->derniere_position_lng,
            'timestamp' => now()->toDateTimeString()
        ];

        // Limiter l'historique aux 100 dernières positions
        if (count($historique) > 100) {
            $historique = array_slice($historique, -100);
        }

        $this->historique_positions = $historique;
    }

    // =====================================
    // MÉTHODES DE CALCUL
    // =====================================

    /**
     * Calculer la distance entre deux points
     */
    public function calculerDistance(
        $lat1 = null, $lon1 = null,
        $lat2 = null, $lon2 = null
    ): float {
        // Si pas de paramètres, utiliser les coordonnées de départ et d'arrivée
        if ($lat1 === null) {
            $lat1 = $this->latitude_depart;
            $lon1 = $this->longitude_depart;
            $lat2 = $this->latitude_livraison;
            $lon2 = $this->longitude_livraison;
        }

        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        // Formule Haversine
        $earthRadius = 6371; // Rayon de la Terre en km

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Calculer la distance restante
     */
    public function calculerDistanceRestante(): float
    {
        if (!$this->tracking_actif || !$this->derniere_position_lat) {
            return $this->distance_km ?? 0;
        }

        return $this->calculerDistance(
            $this->derniere_position_lat,
            $this->derniere_position_lng,
            $this->latitude_livraison,
            $this->longitude_livraison
        );
    }

    /**
     * Estimer la durée de livraison
     */
    public function estimerDuree(): int
    {
        // Vitesse moyenne selon le mode de transport (km/h)
        $vitesses = [
            'velo' => 15,
            'moto' => 30,
            'voiture' => 25,
            'camionnette' => 20,
            'a_pied' => 5
        ];

        $vitesse = $vitesses[$this->mode_transport ?? 'moto'] ?? 25;

        // Ajouter du temps pour le trafic selon l'heure
        $heure = now()->hour;
        $facteurTrafic = 1;

        if (($heure >= 7 && $heure <= 9) || ($heure >= 17 && $heure <= 19)) {
            $facteurTrafic = 1.5; // Heures de pointe
        }

        $dureeBase = ($this->distance_km / $vitesse) * 60; // En minutes

        return (int) round($dureeBase * $facteurTrafic);
    }

    /**
     * Calculer la durée réelle
     */
    protected function calculerDureeReelle(): void
    {
        if ($this->date_depart && $this->date_livraison_effective) {
            $this->duree_reelle_minutes = $this->date_depart->diffInMinutes($this->date_livraison_effective);
        }
    }

    /**
     * Calculer les frais de livraison
     */
    public function calculerFraisLivraison(): float
    {
        // Tarif de base
        $tarifBase = 1000; // 1000 FCFA

        // Tarif par km
        $tarifKm = 200; // 200 FCFA/km

        // Suppléments
        $supplements = 0;

        // Supplément urgence
        if ($this->priorite === 'urgente' || $this->type_livraison === 'express') {
            $supplements += 2000;
        }

        // Supplément fragile
        if ($this->fragile) {
            $supplements += 500;
        }

        // Supplément assurance
        if ($this->assurance_livraison && $this->valeur_declaree) {
            $supplements += $this->valeur_declaree * 0.02; // 2% de la valeur
        }

        // Supplément horaire (nuit ou weekend)
        $heure = now()->hour;
        if ($heure < 6 || $heure > 22) {
            $supplements += 1500; // Supplément nuit
        }

        if (now()->isWeekend()) {
            $supplements += 1000; // Supplément weekend
        }

        // Calcul total
        $frais = $tarifBase + ($this->distance_km * $tarifKm) + $supplements;

        // Arrondir à 100 FCFA près
        return round($frais / 100) * 100;
    }

    // =====================================
    // MÉTHODES DE NOTIFICATION
    // =====================================

    /**
     * Notifier le patient
     */
    protected function notifierPatient(string $titre, string $message): void
    {
        if (!$this->patient_id) {
            return;
        }

        Notification::create([
            'user_id' => $this->patient_id,
            'type' => 'livraison',
            'titre' => $titre,
            'message' => $message,
            'entity_type' => 'livraison',
            'entity_id' => $this->id,
            'data' => [
                'numero_livraison' => $this->numero_livraison,
                'statut' => $this->statut
            ]
        ]);
    }

    /**
     * Notifier le livreur
     */
    protected function notifierLivreur(string $titre, string $message): void
    {
        if (!$this->livreur_id) {
            return;
        }

        Notification::create([
            'user_id' => $this->livreur_id,
            'type' => 'livraison',
            'titre' => $titre,
            'message' => $message,
            'entity_type' => 'livraison',
            'entity_id' => $this->id,
            'priorite' => $this->priorite === 'urgente' ? 'haute' : 'normale',
            'data' => [
                'numero_livraison' => $this->numero_livraison,
                'adresse' => $this->adresse_livraison
            ]
        ]);
    }

    /**
     * Notifier le support
     */
    protected function notifierSupport(string $message): void
    {
        // Notifier tous les administrateurs et support
        $supportUsers = User::role(['admin', 'support'])->get();

        foreach ($supportUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'incident',
                'titre' => 'Problème de livraison',
                'message' => $message,
                'entity_type' => 'livraison',
                'entity_id' => $this->id,
                'priorite' => 'haute'
            ]);
        }
    }

    // =====================================
    // MÉTHODES UTILITAIRES
    // =====================================

    /**
     * Générer un numéro de livraison unique
     */
    public static function generateNumeroLivraison(): string
    {
        $prefix = 'LIV';
        $date = date('Ymd');
        $random = strtoupper(Str::random(6));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Générer un code de validation
     */
    public static function generateCodeValidation(): string
    {
        return strtoupper(Str::random(8));
    }

    /**
     * Vérifier si les coordonnées sont définies
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude_depart && $this->longitude_depart &&
               $this->latitude_livraison && $this->longitude_livraison;
    }

    /**
     * Déterminer la priorité d'un incident
     */
    protected function determinerPrioriteIncident(string $type): string
    {
        $prioritesHautes = [
            'accident', 'vol', 'agression', 'medicament_manquant'
        ];

        $prioritesMoyennes = [
            'retard', 'client_absent', 'adresse_incorrecte'
        ];

        if (in_array($type, $prioritesHautes)) {
            return 'haute';
        }

        if (in_array($type, $prioritesMoyennes)) {
            return 'moyenne';
        }

        return 'basse';
    }

    /**
     * Obtenir l'itinéraire optimisé
     */
    public function getItineraireOptimise(): array
    {
        // Ici on pourrait intégrer Google Maps API ou autre service
        // Pour l'instant, retour simple
        return [
            'depart' => [
                'adresse' => $this->adresse_depart,
                'lat' => $this->latitude_depart,
                'lng' => $this->longitude_depart
            ],
            'arrivee' => [
                'adresse' => $this->adresse_livraison,
                'lat' => $this->latitude_livraison,
                'lng' => $this->longitude_livraison
            ],
            'distance' => $this->distance_km,
            'duree_estimee' => $this->duree_estimee_minutes
        ];
    }

    /**
     * Obtenir le temps restant estimé
     */
    public function getTempsRestantEstime(): ?int
    {
        if (!in_array($this->statut, ['en_route', 'affectee'])) {
            return null;
        }

        if ($this->tracking_actif && $this->derniere_position_lat) {
            $distanceRestante = $this->calculerDistanceRestante();
            $vitesseMoyenne = 25; // km/h

            return (int) round(($distanceRestante / $vitesseMoyenne) * 60);
        }

        return $this->duree_estimee_minutes;
    }

    // =====================================
    // MÉTHODES DE FORMATAGE
    // =====================================

    /**
     * Obtenir le badge de statut
     */
    public function getStatutBadgeAttribute(): string
    {
        $badges = [
            'en_preparation' => '<span class="badge bg-secondary">En préparation</span>',
            'affectee' => '<span class="badge bg-info">Affectée</span>',
            'en_route' => '<span class="badge bg-primary">En route</span>',
            'arrivee' => '<span class="badge bg-warning">Arrivée</span>',
            'livree' => '<span class="badge bg-success">Livrée</span>',
            'annulee' => '<span class="badge bg-danger">Annulée</span>',
            'echec' => '<span class="badge bg-dark">Échec</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    /**
     * Obtenir le badge de priorité
     */
    public function getPrioriteBadgeAttribute(): string
    {
        $badges = [
            'basse' => '<span class="badge bg-secondary">Basse</span>',
            'normale' => '<span class="badge bg-primary">Normale</span>',
            'haute' => '<span class="badge bg-warning">Haute</span>',
            'urgente' => '<span class="badge bg-danger">Urgente</span>',
        ];

        return $badges[$this->priorite] ?? '<span class="badge bg-secondary">Normale</span>';
    }

    /**
     * Obtenir le badge de type
     */
    public function getTypeLivraisonBadgeAttribute(): string
    {
        $badges = [
            'standard' => '<span class="badge bg-primary">Standard</span>',
            'express' => '<span class="badge bg-warning">Express</span>',
            'programmee' => '<span class="badge bg-info">Programmée</span>',
            'fragile' => '<span class="badge bg-danger">Fragile</span>',
        ];

        return $badges[$this->type_livraison] ?? '<span class="badge bg-primary">Standard</span>';
    }

    /**
     * Obtenir l'icône du mode de transport
     */
    public function getModeTransportIconeAttribute(): string
    {
        $icones = [
            'velo' => '<i class="fas fa-bicycle"></i>',
            'moto' => '<i class="fas fa-motorcycle"></i>',
            'voiture' => '<i class="fas fa-car"></i>',
            'camionnette' => '<i class="fas fa-truck"></i>',
            'a_pied' => '<i class="fas fa-walking"></i>',
        ];

        return $icones[$this->mode_transport] ?? '<i class="fas fa-truck"></i>';
    }

    /**
     * Formater l'adresse de livraison
     */
    public function getAdresseCompleteLivraisonAttribute(): string
    {
        $adresse = $this->adresse_livraison;

        if ($this->contact_livraison) {
            $adresse .= "\nContact: " . $this->contact_livraison;
        }

        if ($this->telephone_contact) {
            $adresse .= "\nTél: " . $this->telephone_contact;
        }

        if ($this->instructions_livraison) {
            $adresse .= "\nInstructions: " . $this->instructions_livraison;
        }

        return $adresse;
    }

    /**
     * Obtenir le résumé de la livraison
     */
    public function getResumeAttribute(): string
    {
        return sprintf(
            "Livraison %s - %s - %.2f km - %s",
            $this->numero_livraison,
            $this->statut,
            $this->distance_km,
            $this->date_programmee ? $this->date_programmee->format('d/m/Y H:i') : 'Non programmée'
        );
    }
}
