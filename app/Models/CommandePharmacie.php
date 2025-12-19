<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Models\Livraison;

class CommandePharmacie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'commandes_pharmacie';

    protected $fillable = [
        'numero_commande',
        'patient_id',
        'pharmacie_id',
        'ordonnance_id',
        'type_commande',
        'date_commande',
        'date_preparation',
        'date_livraison_prevue',
        'date_livraison_effective',
        'mode_livraison',
        'adresse_livraison',
        'latitude_livraison',
        'longitude_livraison',
        'contact_livraison',
        'instructions_livraison',
        'montant_produits',
        'frais_livraison',
        'montant_total',
        'statut',
        'statut_paiement',
        'livreur_id',
        'code_retrait',
        'preuve_livraison',
        'signature_client',
        'commentaire_client',
        'note_satisfaction',
        'metadata'
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'date_preparation' => 'datetime',
        'date_livraison_prevue' => 'datetime',
        'date_livraison_effective' => 'datetime',
        'montant_produits' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'latitude_livraison' => 'decimal:8',
        'longitude_livraison' => 'decimal:8',
        'note_satisfaction' => 'integer',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function () {
            throw new \Exception('Le modèle CommandePharmacie est déprécié. Utilisez CommandePharmaceutique.');
        });

        static::creating(function ($commande) {
            if (empty($commande->numero_commande)) {
                $commande->numero_commande = self::generateNumeroCommande();
            }

            if (empty($commande->date_commande)) {
                $commande->date_commande = now();
            }

            if (empty($commande->code_retrait) && $commande->mode_livraison === 'retrait') {
                $commande->code_retrait = self::generateCodeRetrait();
            }

            // Calculer le montant total
            $commande->montant_total = ($commande->montant_produits ?? 0) + ($commande->frais_livraison ?? 0);
        });

        static::updating(function ($commande) {
            // Recalculer le montant total si nécessaire
            if ($commande->isDirty(['montant_produits', 'frais_livraison'])) {
                $commande->montant_total = ($commande->montant_produits ?? 0) + ($commande->frais_livraison ?? 0);
            }
        });
    }

    // Relations
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'pharmacie_id');
    }

    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class);
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(CommandeLigne::class, 'commande_id');
    }

    public function livraison(): HasOne
    {
        return $this->hasOne(Livraison::class, 'commande_id');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'commande_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'entity_id')
                    ->where('entity_type', 'commande');
    }

    // Scopes
    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', ['nouvelle', 'confirmee', 'en_preparation', 'prete', 'en_livraison']);
    }

    public function scopeLivrees($query)
    {
        return $query->where('statut', 'livree');
    }

    public function scopeAnnulees($query)
    {
        return $query->where('statut', 'annulee');
    }

    public function scopePatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopePharmacie($query, $pharmacieId)
    {
        return $query->where('pharmacie_id', $pharmacieId);
    }

    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_commande', today());
    }

    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date_commande', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Méthodes de génération
    public static function generateNumeroCommande(): string
    {
        $prefix = 'CMD';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(4));

        return "{$prefix}-{$timestamp}-{$random}";
    }

    public static function generateCodeRetrait(): string
    {
        return strtoupper(Str::random(8));
    }

    // Méthodes de gestion du statut
    public function confirmer(): void
    {
        if ($this->statut !== 'nouvelle') {
            throw new \Exception("La commande ne peut être confirmée que si elle est nouvelle");
        }

        $this->statut = 'confirmee';
        $this->save();

        $this->notifierPatient('Commande confirmée',
            "Votre commande {$this->numero_commande} a été confirmée par la pharmacie");
    }

    public function demarrerPreparation(): void
    {
        if (!in_array($this->statut, ['confirmee', 'nouvelle'])) {
            throw new \Exception("La commande doit être confirmée pour démarrer la préparation");
        }

        $this->statut = 'en_preparation';
        $this->date_preparation = now();
        $this->save();

        $this->notifierPatient('Préparation en cours',
            "La préparation de votre commande {$this->numero_commande} a commencé");
    }

    public function marquerPrete(): void
    {
        if ($this->statut !== 'en_preparation') {
            throw new \Exception("La commande doit être en préparation pour être marquée comme prête");
        }

        $this->statut = 'prete';
        $this->save();

        if ($this->mode_livraison === 'retrait') {
            $this->notifierPatient('Commande prête',
                "Votre commande {$this->numero_commande} est prête. Code de retrait: {$this->code_retrait}");
        } else {
            $this->notifierPatient('Commande prête',
                "Votre commande {$this->numero_commande} est prête et sera bientôt livrée");
        }
    }

    public function demarrerLivraison(int $livreurId = null): void
    {
        if ($this->mode_livraison === 'retrait') {
            throw new \Exception("Une commande en retrait ne peut pas être livrée");
        }

        if (!in_array($this->statut, ['prete', 'confirmee'])) {
            throw new \Exception("La commande doit être prête pour démarrer la livraison");
        }

        $this->statut = 'en_livraison';
        $this->livreur_id = $livreurId;
        $this->save();

        $this->notifierPatient('Livraison en cours',
            "Votre commande {$this->numero_commande} est en cours de livraison");
    }

    public function marquerLivree(array $preuves = []): void
    {
        if ($this->mode_livraison === 'livraison' && $this->statut !== 'en_livraison') {
            throw new \Exception("La commande doit être en livraison pour être marquée comme livrée");
        }

        if ($this->mode_livraison === 'retrait' && $this->statut !== 'prete') {
            throw new \Exception("La commande doit être prête pour être retirée");
        }

        $this->statut = 'livree';
        $this->date_livraison_effective = now();

        if (!empty($preuves['signature'])) {
            $this->signature_client = $preuves['signature'];
        }

        if (!empty($preuves['photo'])) {
            $this->preuve_livraison = $preuves['photo'];
        }

        $this->save();

        $this->notifierPatient('Commande livrée',
            "Votre commande {$this->numero_commande} a été livrée avec succès");

        // Marquer l'ordonnance comme dispensée si applicable
        if ($this->ordonnance) {
            $this->ordonnance->marquerDispensee($this->pharmacie_id);
        }
    }

    public function annuler(string $motif, string $initiateur = 'patient'): void
    {
        if (in_array($this->statut, ['livree', 'annulee'])) {
            throw new \Exception("Cette commande ne peut plus être annulée");
        }

        $ancienStatut = $this->statut;
        $this->statut = 'annulee';

        $metadata = $this->metadata ?? [];
        $metadata['annulation'] = [
            'motif' => $motif,
            'initiateur' => $initiateur,
            'date' => now()->toDateTimeString(),
            'ancien_statut' => $ancienStatut
        ];
        $this->metadata = $metadata;

        $this->save();

        // Rembourser si nécessaire
        if ($this->statut_paiement === 'paye') {
            $this->initierRemboursement($motif);
        }

        $destinataire = $initiateur === 'patient' ? 'pharmacie' : 'patient';
        $this->notifier($destinataire, 'Commande annulée',
            "La commande {$this->numero_commande} a été annulée. Motif: {$motif}");
    }

    // Méthodes de calcul
    public function calculerFraisLivraison(): float
    {
        if ($this->mode_livraison === 'retrait') {
            return 0;
        }

        // Calcul basé sur la distance
        if ($this->latitude_livraison && $this->longitude_livraison && $this->pharmacie) {
            $distance = $this->calculerDistance(
                $this->pharmacie->latitude,
                $this->pharmacie->longitude,
                $this->latitude_livraison,
                $this->longitude_livraison
            );

            // Tarification par paliers
            if ($distance <= 2) {
                return 1000; // 1000 FCFA pour moins de 2km
            } elseif ($distance <= 5) {
                return 2000; // 2000 FCFA pour 2-5km
            } elseif ($distance <= 10) {
                return 3000; // 3000 FCFA pour 5-10km
            } else {
                return 3000 + (($distance - 10) * 200); // +200 FCFA par km au-delà de 10km
            }
        }

        // Frais par défaut
        return 2000;
    }

    private function calculerDistance($lat1, $lon1, $lat2, $lon2): float
    {
        // Formule Haversine pour calculer la distance
        $earthRadius = 6371; // Rayon de la Terre en km

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function getDelaiLivraisonAttribute(): ?string
    {
        if (!$this->date_livraison_effective || !$this->date_commande) {
            return null;
        }

        $delai = $this->date_commande->diffInMinutes($this->date_livraison_effective);

        if ($delai < 60) {
            return "{$delai} minutes";
        } elseif ($delai < 1440) {
            $heures = round($delai / 60, 1);
            return "{$heures} heures";
        } else {
            $jours = round($delai / 1440, 1);
            return "{$jours} jours";
        }
    }

    // Méthodes de notification
    private function notifierPatient(string $titre, string $message): void
    {
        Notification::create([
            'user_id' => $this->patient_id,
            'type' => 'commande',
            'titre' => $titre,
            'message' => $message,
            'entity_type' => 'commande',
            'entity_id' => $this->id,
            'data' => ['numero_commande' => $this->numero_commande]
        ]);
    }

    private function notifier(string $destinataire, string $titre, string $message): void
    {
        $userId = $destinataire === 'patient' ? $this->patient_id : $this->pharmacie->responsable_id;

        Notification::create([
            'user_id' => $userId,
            'type' => 'commande',
            'titre' => $titre,
            'message' => $message,
            'entity_type' => 'commande',
            'entity_id' => $this->id,
            'data' => ['numero_commande' => $this->numero_commande]
        ]);
    }

    // Méthodes de paiement
    public function initierRemboursement(string $motif): void
    {
        // Logique de remboursement à implémenter
        $metadata = $this->metadata ?? [];
        $metadata['remboursement'] = [
            'motif' => $motif,
            'date_demande' => now()->toDateTimeString(),
            'statut' => 'en_attente'
        ];
        $this->metadata = $metadata;
        $this->save();
    }

    // Méthodes de formatage
    public function getStatutBadgeAttribute(): string
    {
        $badges = [
            'nouvelle' => '<span class="badge bg-primary">Nouvelle</span>',
            'confirmee' => '<span class="badge bg-info">Confirmée</span>',
            'en_preparation' => '<span class="badge bg-warning">En préparation</span>',
            'prete' => '<span class="badge bg-success">Prête</span>',
            'en_livraison' => '<span class="badge bg-info">En livraison</span>',
            'livree' => '<span class="badge bg-success">Livrée</span>',
            'annulee' => '<span class="badge bg-danger">Annulée</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getModeLivraisonBadgeAttribute(): string
    {
        $badges = [
            'livraison' => '<span class="badge bg-primary"><i class="fas fa-truck"></i> Livraison</span>',
            'retrait' => '<span class="badge bg-info"><i class="fas fa-store"></i> Retrait</span>',
        ];

        return $badges[$this->mode_livraison] ?? '<span class="badge bg-secondary">Non défini</span>';
    }

    public function getStatutPaiementBadgeAttribute(): string
    {
        $badges = [
            'en_attente' => '<span class="badge bg-warning">En attente</span>',
            'paye' => '<span class="badge bg-success">Payé</span>',
            'echoue' => '<span class="badge bg-danger">Échoué</span>',
            'rembourse' => '<span class="badge bg-info">Remboursé</span>',
        ];

        return $badges[$this->statut_paiement] ?? '<span class="badge bg-secondary">Non défini</span>';
    }
}
