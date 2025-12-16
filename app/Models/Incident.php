<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\CommentaireIncident;


class Incident extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'incidents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'numero_incident',
        'titre',
        'description',
        'type_incident',
        'categorie',
        'sous_categorie',
        'statut',
        'priorite',
        'severite',
        'impact',

        // Relations polymorphes
        'entity_type',
        'entity_id',

        // Personnes impliquées
        'declarant_id',
        'patient_id',
        'praticien_id',
        'livreur_id',
        'structure_id',
        'assigne_a',
        'resolu_par',
        'valide_par',

        // Dates importantes
        'date_incident',
        'date_declaration',
        'date_prise_en_charge',
        'date_resolution',
        'date_cloture',
        'date_escalade',
        'delai_resolution_heures',
        'sla_heures',

        // Informations de résolution
        'cause_racine',
        'resolution',
        'actions_correctives',
        'actions_preventives',
        'cout_incident',
        'temps_resolution_minutes',

        // Escalade
        'niveau_escalade',
        'raison_escalade',
        'escalade_vers',

        // Évaluation et feedback
        'satisfaction_client',
        'commentaire_client',
        'lecons_apprises',

        // Documents et preuves
        'pieces_jointes',
        'captures_ecran',
        'logs_techniques',

        // Localisation (si applicable)
        'lieu_incident',
        'latitude',
        'longitude',

        // Indicateurs
        'recurrent',
        'nombre_occurrences',
        'client_impacte',
        'service_impacte',
        'perte_financiere',
        'remboursement_effectue',
        'compensation_offerte',

        // Workflow
        'etape_workflow',
        'validation_requise',
        'approuve',

        // Métadonnées
        'tags',
        'source_declaration',
        'canal_declaration',
        'reference_externe',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_incident' => 'datetime',
        'date_declaration' => 'datetime',
        'date_prise_en_charge' => 'datetime',
        'date_resolution' => 'datetime',
        'date_cloture' => 'datetime',
        'date_escalade' => 'datetime',

        'delai_resolution_heures' => 'integer',
        'sla_heures' => 'integer',
        'temps_resolution_minutes' => 'integer',
        'niveau_escalade' => 'integer',
        'satisfaction_client' => 'integer',
        'nombre_occurrences' => 'integer',

        'cout_incident' => 'decimal:2',
        'perte_financiere' => 'decimal:2',
        'compensation_offerte' => 'decimal:2',

        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',

        'recurrent' => 'boolean',
        'client_impacte' => 'boolean',
        'service_impacte' => 'boolean',
        'remboursement_effectue' => 'boolean',
        'validation_requise' => 'boolean',
        'approuve' => 'boolean',

        'pieces_jointes' => 'array',
        'captures_ecran' => 'array',
        'logs_techniques' => 'array',
        'actions_correctives' => 'array',
        'actions_preventives' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'statut' => 'nouveau',
        'priorite' => 'moyenne',
        'severite' => 'mineure',
        'impact' => 'faible',
        'niveau_escalade' => 0,
        'recurrent' => false,
        'nombre_occurrences' => 1,
        'client_impacte' => false,
        'service_impacte' => false,
        'remboursement_effectue' => false,
        'validation_requise' => false,
        'approuve' => false,
    ];

    /**
     * Les types d'incidents disponibles
     */
    const TYPES_INCIDENTS = [
        'livraison' => 'Problème de livraison',
        'medical' => 'Incident médical',
        'paiement' => 'Problème de paiement',
        'technique' => 'Problème technique',
        'qualite' => 'Problème de qualité',
        'comportement' => 'Problème comportemental',
        'securite' => 'Problème de sécurité',
        'donnees' => 'Problème de données',
        'assurance' => 'Problème d\'assurance',
        'autre' => 'Autre',
    ];

    /**
     * Les statuts possibles
     */
    const STATUTS = [
        'nouveau' => 'Nouveau',
        'ouvert' => 'Ouvert',
        'en_cours' => 'En cours',
        'en_attente' => 'En attente',
        'escalade' => 'Escaladé',
        'resolu' => 'Résolu',
        'clos' => 'Clôturé',
        'annule' => 'Annulé',
        'rejete' => 'Rejeté',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($incident) {
            if (empty($incident->numero_incident)) {
                $incident->numero_incident = self::generateNumeroIncident();
            }

            if (empty($incident->date_declaration)) {
                $incident->date_declaration = now();
            }

            if (empty($incident->date_incident)) {
                $incident->date_incident = now();
            }

            // Définir le SLA selon la priorité
            if (empty($incident->sla_heures)) {
                $incident->sla_heures = self::definirSLA($incident->priorite);
            }
        });

        static::updating(function ($incident) {
            // Calculer le temps de résolution si résolu
            if ($incident->isDirty('statut') && $incident->statut === 'resolu') {
                $incident->calculerTempsResolution();
            }

            // Vérifier si l'incident est récurrent
            if ($incident->isDirty('type_incident') || $incident->isDirty('patient_id')) {
                $incident->verifierRecurrence();
            }
        });

        static::created(function ($incident) {
            // Notifier les personnes concernées
            $incident->notifierCreation();

            // Créer automatiquement les tâches de résolution
            $incident->creerTachesResolution();
        });
    }

    // =====================================
    // RELATIONS
    // =====================================

    /**
     * Entité concernée (polymorphe)
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Déclarant de l'incident
     */
    public function declarant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'declarant_id');
    }

    /**
     * Patient concerné
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Praticien concerné
     */
    public function praticien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'praticien_id');
    }

    /**
     * Livreur concerné
     */
    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    /**
     * Structure médicale concernée
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'structure_id');
    }

    /**
     * Agent assigné
     */
    public function assigneA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigne_a');
    }

    /**
     * Agent qui a résolu
     */
    public function resoluPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolu_par');
    }

    /**
     * Validateur
     */
    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * Commentaires sur l'incident
     */
    public function commentaires(): HasMany
    {
        return $this->hasMany(CommentaireIncident::class);
    }

    /**
     * Historique des actions
     */
    public function historique(): HasMany
    {
        return $this->hasMany(HistoriqueIncident::class);
    }

    /**
     * Notifications liées
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'entity_id')
                    ->where('entity_type', 'incident');
    }

    /**
     * Tâches de résolution
     */
    public function taches(): HasMany
    {
        return $this->hasMany(TacheResolution::class);
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Incidents ouverts
     */
    public function scopeOuverts($query)
    {
        return $query->whereIn('statut', ['nouveau', 'ouvert', 'en_cours', 'en_attente']);
    }

    /**
     * Incidents résolus
     */
    public function scopeResolus($query)
    {
        return $query->where('statut', 'resolu');
    }

    /**
     * Incidents critiques
     */
    public function scopeCritiques($query)
    {
        return $query->whereIn('priorite', ['critique', 'haute'])
                    ->orWhereIn('severite', ['majeure', 'critique']);
    }

    /**
     * Incidents en retard
     */
    public function scopeEnRetard($query)
    {
        return $query->whereNotIn('statut', ['resolu', 'clos', 'annule', 'rejete'])
                    ->whereNotNull('sla_heures')
                    ->whereRaw('TIMESTAMPDIFF(HOUR, date_declaration, NOW()) > sla_heures');
    }

    /**
     * Incidents par type
     */
    public function scopeParType($query, string $type)
    {
        return $query->where('type_incident', $type);
    }

    /**
     * Incidents récurrents
     */
    public function scopeRecurrents($query)
    {
        return $query->where('recurrent', true)
                    ->orWhere('nombre_occurrences', '>', 1);
    }

    /**
     * Incidents de la période
     */
    public function scopePeriode($query, $dateDebut, $dateFin = null)
    {
        $query->where('date_incident', '>=', $dateDebut);

        if ($dateFin) {
            $query->where('date_incident', '<=', $dateFin);
        }

        return $query;
    }

    /**
     * Incidents assignés à un agent
     */
    public function scopeAssigneA($query, $userId)
    {
        return $query->where('assigne_a', $userId);
    }

    // =====================================
    // MÉTHODES DE GESTION DU WORKFLOW
    // =====================================

    /**
     * Prendre en charge l'incident
     */
    public function prendreEnCharge(User $agent): void
    {
        if (!in_array($this->statut, ['nouveau', 'ouvert'])) {
            throw new \Exception("L'incident ne peut être pris en charge dans son état actuel");
        }

        $this->assigne_a = $agent->id;
        $this->statut = 'en_cours';
        $this->date_prise_en_charge = now();
        $this->save();

        $this->ajouterHistorique('prise_en_charge', "Incident pris en charge par {$agent->nom}");
        $this->notifierPriseEnCharge($agent);
    }

    /**
     * Mettre l'incident en attente
     */
    public function mettreEnAttente(string $raison): void
    {
        if ($this->statut !== 'en_cours') {
            throw new \Exception("Seul un incident en cours peut être mis en attente");
        }

        $ancienStatut = $this->statut;
        $this->statut = 'en_attente';

        $metadata = $this->metadata ?? [];
        $metadata['mise_en_attente'] = [
            'date' => now()->toDateTimeString(),
            'raison' => $raison,
            'ancien_statut' => $ancienStatut
        ];
        $this->metadata = $metadata;
        $this->save();

        $this->ajouterHistorique('mise_en_attente', "Incident mis en attente : {$raison}");
    }

    /**
     * Reprendre un incident en attente
     */
    public function reprendre(): void
    {
        if ($this->statut !== 'en_attente') {
            throw new \Exception("Seul un incident en attente peut être repris");
        }

        $this->statut = 'en_cours';
        $this->save();

        $this->ajouterHistorique('reprise', "Incident repris");
    }

    /**
     * Escalader l'incident
     */
    public function escalader(string $raison, int $niveau = null, User $escaladeVers = null): void
    {
        if (in_array($this->statut, ['resolu', 'clos', 'annule'])) {
            throw new \Exception("Cet incident ne peut plus être escaladé");
        }

        $this->statut = 'escalade';
        $this->niveau_escalade = $niveau ?? ($this->niveau_escalade + 1);
        $this->raison_escalade = $raison;
        $this->date_escalade = now();

        if ($escaladeVers) {
            $this->escalade_vers = $escaladeVers->id;
            $this->assigne_a = $escaladeVers->id;
        }

        $this->save();

        $this->ajouterHistorique('escalade', "Incident escaladé au niveau {$this->niveau_escalade} : {$raison}");
        $this->notifierEscalade();
    }

    /**
     * Résoudre l'incident
     */
    public function resoudre(string $resolution, array $actions = []): void
    {
        if (in_array($this->statut, ['resolu', 'clos', 'annule'])) {
            throw new \Exception("Cet incident est déjà résolu ou clôturé");
        }

        $this->statut = 'resolu';
        $this->resolution = $resolution;
        $this->date_resolution = now();
        $this->resolu_par = auth()->id();

        if (!empty($actions['correctives'])) {
            $this->actions_correctives = $actions['correctives'];
        }

        if (!empty($actions['preventives'])) {
            $this->actions_preventives = $actions['preventives'];
        }

        if (!empty($actions['cause_racine'])) {
            $this->cause_racine = $actions['cause_racine'];
        }

        $this->save();

        $this->ajouterHistorique('resolution', "Incident résolu : {$resolution}");
        $this->notifierResolution();
    }

    /**
     * Clôturer l'incident
     */
    public function cloturer(string $commentaire = null): void
    {
        if ($this->statut !== 'resolu') {
            throw new \Exception("Seul un incident résolu peut être clôturé");
        }

        $this->statut = 'clos';
        $this->date_cloture = now();

        if ($commentaire) {
            $metadata = $this->metadata ?? [];
            $metadata['commentaire_cloture'] = $commentaire;
            $this->metadata = $metadata;
        }

        $this->save();

        $this->ajouterHistorique('cloture', "Incident clôturé" . ($commentaire ? " : {$commentaire}" : ""));
    }

    /**
     * Rejeter l'incident
     */
    public function rejeter(string $motif): void
    {
        if (in_array($this->statut, ['resolu', 'clos'])) {
            throw new \Exception("Cet incident ne peut plus être rejeté");
        }

        $this->statut = 'rejete';

        $metadata = $this->metadata ?? [];
        $metadata['rejet'] = [
            'date' => now()->toDateTimeString(),
            'motif' => $motif,
            'par' => auth()->id()
        ];
        $this->metadata = $metadata;
        $this->save();

        $this->ajouterHistorique('rejet', "Incident rejeté : {$motif}");
    }

    /**
     * Réouvrir l'incident
     */
    public function reouvrir(string $raison): void
    {
        if (!in_array($this->statut, ['resolu', 'clos', 'rejete'])) {
            throw new \Exception("Seul un incident résolu, clôturé ou rejeté peut être réouvert");
        }

        $ancienStatut = $this->statut;
        $this->statut = 'ouvert';
        $this->nombre_occurrences++;
        $this->recurrent = true;

        $metadata = $this->metadata ?? [];
        $metadata['reouvertures'] = $metadata['reouvertures'] ?? [];
        $metadata['reouvertures'][] = [
            'date' => now()->toDateTimeString(),
            'raison' => $raison,
            'ancien_statut' => $ancienStatut,
            'par' => auth()->id()
        ];
        $this->metadata = $metadata;
        $this->save();

        $this->ajouterHistorique('reouverture', "Incident réouvert : {$raison}");
        $this->notifierReouverture();
    }

    // =====================================
    // MÉTHODES DE CALCUL ET VÉRIFICATION
    // =====================================

    /**
     * Définir le SLA selon la priorité
     */
    public static function definirSLA(string $priorite): int
    {
        $sla = [
            'critique' => 2,    // 2 heures
            'haute' => 4,       // 4 heures
            'moyenne' => 24,    // 24 heures
            'basse' => 72,      // 72 heures
        ];

        return $sla[$priorite] ?? 24;
    }

    /**
     * Calculer le temps de résolution
     */
    protected function calculerTempsResolution(): void
    {
        if ($this->date_prise_en_charge && $this->date_resolution) {
            $this->temps_resolution_minutes = $this->date_prise_en_charge->diffInMinutes($this->date_resolution);
            $this->delai_resolution_heures = $this->date_declaration->diffInHours($this->date_resolution);
        }
    }

    /**
     * Vérifier si l'incident est récurrent
     */
    protected function verifierRecurrence(): void
    {
        // Chercher des incidents similaires dans les 30 derniers jours
        $incidentsSimilaires = self::where('id', '!=', $this->id)
            ->where('type_incident', $this->type_incident)
            ->where(function($query) {
                $query->where('patient_id', $this->patient_id)
                      ->orWhere('praticien_id', $this->praticien_id)
                      ->orWhere('structure_id', $this->structure_id);
            })
            ->where('date_incident', '>=', now()->subDays(30))
            ->count();

        if ($incidentsSimilaires > 0) {
            $this->recurrent = true;
            $this->nombre_occurrences = $incidentsSimilaires + 1;
        }
    }

    /**
     * Vérifier si le SLA est respecté
     */
    public function estDansSLA(): bool
    {
        if (in_array($this->statut, ['resolu', 'clos'])) {
            return $this->delai_resolution_heures <= $this->sla_heures;
        }

        $heuresEcoulees = $this->date_declaration->diffInHours(now());
        return $heuresEcoulees <= $this->sla_heures;
    }

    /**
     * Obtenir le temps restant SLA
     */
    public function getTempsRestantSLA(): ?int
    {
        if (in_array($this->statut, ['resolu', 'clos', 'annule', 'rejete'])) {
            return null;
        }

        $heuresEcoulees = $this->date_declaration->diffInHours(now());
        $heuresRestantes = $this->sla_heures - $heuresEcoulees;

        return max(0, $heuresRestantes);
    }

    /**
     * Calculer l'impact financier
     */
    public function calculerImpactFinancier(): float
    {
        $impact = 0;

        // Coût direct de l'incident
        if ($this->cout_incident) {
            $impact += $this->cout_incident;
        }

        // Perte financière
        if ($this->perte_financiere) {
            $impact += $this->perte_financiere;
        }

        // Compensation offerte
        if ($this->compensation_offerte) {
            $impact += $this->compensation_offerte;
        }

        // Remboursement
        if ($this->remboursement_effectue && $this->entity) {
            if ($this->entity instanceof Facture) {
                $impact += $this->entity->montant_total;
            } elseif ($this->entity instanceof CommandePharmacie) {
                $impact += $this->entity->montant_total;
            }
        }

        return $impact;
    }

    // =====================================
    // MÉTHODES D'HISTORIQUE ET COMMENTAIRES
    // =====================================

    /**
     * Ajouter un commentaire
     */
    public function ajouterCommentaire(string $commentaire, User $auteur = null): void
    {
        $this->commentaires()->create([
            'commentaire' => $commentaire,
            'auteur_id' => $auteur ? $auteur->id : auth()->id(),
            'visible_client' => false,
        ]);
    }

    /**
     * Ajouter un commentaire visible au client
     */
    public function ajouterCommentairePublic(string $commentaire, User $auteur = null): void
    {
        $this->commentaires()->create([
            'commentaire' => $commentaire,
            'auteur_id' => $auteur ? $auteur->id : auth()->id(),
            'visible_client' => true,
        ]);

        // Notifier le client
        if ($this->patient_id) {
            $this->notifierPatient('Nouveau commentaire',
                "Un nouveau commentaire a été ajouté à votre incident #{$this->numero_incident}");
        }
    }

    /**
     * Ajouter une entrée à l'historique
     */
    protected function ajouterHistorique(string $action, string $description): void
    {
        $this->historique()->create([
            'action' => $action,
            'description' => $description,
            'user_id' => auth()->id(),
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        ]);
    }

    // =====================================
    // MÉTHODES DE NOTIFICATION
    // =====================================

    /**
     * Notifier la création de l'incident
     */
    protected function notifierCreation(): void
    {
        // Notifier les administrateurs pour les incidents critiques
        if (in_array($this->priorite, ['critique', 'haute'])) {
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'incident_critique',
                    'titre' => 'Incident critique créé',
                    'message' => "Un incident {$this->priorite} a été créé : {$this->titre}",
                    'entity_type' => 'incident',
                    'entity_id' => $this->id,
                    'priorite' => 'haute',
                ]);
            }
        }

        // Notifier le support
        $this->notifierSupport("Nouvel incident #{$this->numero_incident} : {$this->titre}");
    }

    /**
     * Notifier la prise en charge
     */
    protected function notifierPriseEnCharge(User $agent): void
    {
        // Notifier le déclarant
        if ($this->declarant_id) {
            Notification::create([
                'user_id' => $this->declarant_id,
                'type' => 'incident_pris_en_charge',
                'titre' => 'Incident pris en charge',
                'message' => "Votre incident #{$this->numero_incident} a été pris en charge par {$agent->nom}",
                'entity_type' => 'incident',
                'entity_id' => $this->id,
            ]);
        }
    }

    /**
     * Notifier l'escalade
     */
    protected function notifierEscalade(): void
    {
        // Notifier le niveau supérieur
        if ($this->escalade_vers) {
            Notification::create([
                'user_id' => $this->escalade_vers,
                'type' => 'incident_escalade',
                'titre' => 'Incident escaladé',
                'message' => "L'incident #{$this->numero_incident} vous a été escaladé : {$this->raison_escalade}",
                'entity_type' => 'incident',
                'entity_id' => $this->id,
                'priorite' => 'haute',
            ]);
        }

        // Notifier les managers
        $managers = User::role('manager')->get();
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'type' => 'incident_escalade',
                'titre' => 'Escalade d\'incident',
                'message' => "L'incident #{$this->numero_incident} a été escaladé au niveau {$this->niveau_escalade}",
                'entity_type' => 'incident',
                'entity_id' => $this->id,
            ]);
        }
    }

    /**
     * Notifier la résolution
     */
    protected function notifierResolution(): void
    {
        // Notifier le déclarant
        if ($this->declarant_id) {
            Notification::create([
                'user_id' => $this->declarant_id,
                'type' => 'incident_resolu',
                'titre' => 'Incident résolu',
                'message' => "Votre incident #{$this->numero_incident} a été résolu",
                'entity_type' => 'incident',
                'entity_id' => $this->id,
            ]);
        }

        // Notifier le patient si concerné
        if ($this->patient_id && $this->patient_id !== $this->declarant_id) {
            $this->notifierPatient('Incident résolu',
                "L'incident concernant votre dossier a été résolu");
        }
    }

    /**
     * Notifier la réouverture
     */
    protected function notifierReouverture(): void
    {
        // Notifier l'agent précédemment assigné
        if ($this->assigne_a) {
            Notification::create([
                'user_id' => $this->assigne_a,
                'type' => 'incident_reouvert',
                'titre' => 'Incident réouvert',
                'message' => "L'incident #{$this->numero_incident} a été réouvert",
                'entity_type' => 'incident',
                'entity_id' => $this->id,
                'priorite' => 'haute',
            ]);
        }
    }

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
            'type' => 'incident',
            'titre' => $titre,
            'message' => $message,
            'entity_type' => 'incident',
            'entity_id' => $this->id,
        ]);
    }

    /**
     * Notifier le support
     */
    protected function notifierSupport(string $message): void
    {
        $supportUsers = User::role(['support', 'admin'])->get();

        foreach ($supportUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'incident_support',
                'titre' => 'Incident support',
                'message' => $message,
                'entity_type' => 'incident',
                'entity_id' => $this->id,
                'priorite' => $this->priorite === 'critique' ? 'haute' : 'normale',
            ]);
        }
    }

    // =====================================
    // MÉTHODES DE TÂCHES ET ACTIONS
    // =====================================

    /**
     * Créer les tâches de résolution automatiques
     */
    protected function creerTachesResolution(): void
    {
        $tachesParType = [
            'livraison' => [
                'Contacter le client',
                'Vérifier le statut de la livraison',
                'Contacter le livreur',
                'Proposer une solution',
            ],
            'medical' => [
                'Analyser le dossier médical',
                'Contacter le praticien',
                'Vérifier les protocoles',
                'Documenter l\'incident',
            ],
            'paiement' => [
                'Vérifier la transaction',
                'Contacter la banque/opérateur',
                'Traiter le remboursement si nécessaire',
                'Confirmer la résolution avec le client',
            ],
            'technique' => [
                'Identifier le bug',
                'Corriger le problème',
                'Tester la solution',
                'Déployer le correctif',
            ],
        ];

        $taches = $tachesParType[$this->type_incident] ?? [
            'Analyser l\'incident',
            'Identifier la cause',
            'Proposer une solution',
            'Implémenter la solution',
            'Vérifier la résolution',
        ];

        foreach ($taches as $index => $tache) {
            $this->taches()->create([
                'titre' => $tache,
                'ordre' => $index + 1,
                'statut' => 'a_faire',
                'obligatoire' => true,
            ]);
        }
    }

    /**
     * Obtenir les actions suggérées
     */
    public function getActionsSuggerees(): array
    {
        $actions = [];

        // Actions selon le statut
        switch ($this->statut) {
            case 'nouveau':
            case 'ouvert':
                $actions[] = ['action' => 'prendre_en_charge', 'label' => 'Prendre en charge'];
                break;

            case 'en_cours':
                $actions[] = ['action' => 'resoudre', 'label' => 'Résoudre'];
                $actions[] = ['action' => 'mettre_en_attente', 'label' => 'Mettre en attente'];
                $actions[] = ['action' => 'escalader', 'label' => 'Escalader'];
                break;

            case 'en_attente':
                $actions[] = ['action' => 'reprendre', 'label' => 'Reprendre'];
                break;

            case 'resolu':
                $actions[] = ['action' => 'cloturer', 'label' => 'Clôturer'];
                $actions[] = ['action' => 'reouvrir', 'label' => 'Réouvrir'];
                break;
        }

        // Actions communes
        if (!in_array($this->statut, ['clos', 'annule'])) {
            $actions[] = ['action' => 'commenter', 'label' => 'Ajouter un commentaire'];
            $actions[] = ['action' => 'attacher_document', 'label' => 'Attacher un document'];
        }

        return $actions;
    }

    // =====================================
    // MÉTHODES UTILITAIRES
    // =====================================

    /**
     * Générer un numéro d'incident unique
     */
    public static function generateNumeroIncident(): string
    {
        $prefix = 'INC';
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        $sequence = str_pad($count, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$sequence}";
    }

    /**
     * Obtenir la couleur de priorité
     */
    public function getCouleurPriorite(): string
    {
        $couleurs = [
            'critique' => '#dc3545', // Rouge
            'haute' => '#fd7e14',    // Orange
            'moyenne' => '#ffc107',   // Jaune
            'basse' => '#28a745',     // Vert
        ];

        return $couleurs[$this->priorite] ?? '#6c757d'; // Gris par défaut
    }

    /**
     * Obtenir l'icône du type
     */
    public function getIconeType(): string
    {
        $icones = [
            'livraison' => 'fa-truck',
            'medical' => 'fa-stethoscope',
            'paiement' => 'fa-credit-card',
            'technique' => 'fa-bug',
            'qualite' => 'fa-star',
            'comportement' => 'fa-user-times',
            'securite' => 'fa-shield-alt',
            'donnees' => 'fa-database',
            'assurance' => 'fa-umbrella',
            'autre' => 'fa-question-circle',
        ];

        return $icones[$this->type_incident] ?? 'fa-exclamation-triangle';
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
            'nouveau' => '<span class="badge bg-primary">Nouveau</span>',
            'ouvert' => '<span class="badge bg-info">Ouvert</span>',
            'en_cours' => '<span class="badge bg-warning">En cours</span>',
            'en_attente' => '<span class="badge bg-secondary">En attente</span>',
            'escalade' => '<span class="badge bg-danger">Escaladé</span>',
            'resolu' => '<span class="badge bg-success">Résolu</span>',
            'clos' => '<span class="badge bg-dark">Clôturé</span>',
            'annule' => '<span class="badge bg-light text-dark">Annulé</span>',
            'rejete' => '<span class="badge bg-danger">Rejeté</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    /**
     * Obtenir le badge de priorité
     */
    public function getPrioriteBadgeAttribute(): string
    {
        $badges = [
            'critique' => '<span class="badge bg-danger"><i class="fas fa-exclamation-circle"></i> Critique</span>',
            'haute' => '<span class="badge bg-warning">Haute</span>',
            'moyenne' => '<span class="badge bg-info">Moyenne</span>',
            'basse' => '<span class="badge bg-success">Basse</span>',
        ];

        return $badges[$this->priorite] ?? '<span class="badge bg-secondary">Non définie</span>';
    }

    /**
     * Obtenir le badge de sévérité
     */
    public function getSeveriteBadgeAttribute(): string
    {
        $badges = [
            'critique' => '<span class="badge bg-danger">Critique</span>',
            'majeure' => '<span class="badge bg-warning">Majeure</span>',
            'mineure' => '<span class="badge bg-info">Mineure</span>',
            'cosmétique' => '<span class="badge bg-light text-dark">Cosmétique</span>',
        ];

        return $badges[$this->severite] ?? '<span class="badge bg-secondary">Non définie</span>';
    }

    /**
     * Obtenir le badge SLA
     */
    public function getSLABadgeAttribute(): string
    {
        if (in_array($this->statut, ['resolu', 'clos', 'annule', 'rejete'])) {
            if ($this->estDansSLA()) {
                return '<span class="badge bg-success"><i class="fas fa-check"></i> SLA respecté</span>';
            } else {
                return '<span class="badge bg-danger"><i class="fas fa-times"></i> SLA dépassé</span>';
            }
        }

        $tempsRestant = $this->getTempsRestantSLA();

        if ($tempsRestant === null) {
            return '';
        }

        if ($tempsRestant <= 0) {
            return '<span class="badge bg-danger"><i class="fas fa-clock"></i> SLA dépassé</span>';
        } elseif ($tempsRestant <= 2) {
            return '<span class="badge bg-warning"><i class="fas fa-clock"></i> ' . $tempsRestant . 'h restantes</span>';
        } else {
            return '<span class="badge bg-info"><i class="fas fa-clock"></i> ' . $tempsRestant . 'h restantes</span>';
        }
    }

    /**
     * Obtenir le résumé de l'incident
     */
    public function getResumeAttribute(): string
    {
        return sprintf(
            "Incident #%s - %s - %s - %s",
            $this->numero_incident,
            self::TYPES_INCIDENTS[$this->type_incident] ?? 'Autre',
            ucfirst($this->priorite),
            $this->statut
        );
    }
}
