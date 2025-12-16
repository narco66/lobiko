<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class CommandePharmaceutique extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'commandes_pharmaceutiques';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero_commande',
        'patient_id',
        'pharmacie_id',
        'ordonnance_id',
        'montant_total',
        'montant_assurance',
        'montant_patient',
        'mode_retrait',
        'adresse_livraison',
        'latitude_livraison',
        'longitude_livraison',
        'frais_livraison',
        'statut',
        'date_commande',
        'date_preparation',
        'date_retrait_prevue',
        'date_livraison_prevue',
        'date_livraison_effective',
        'code_retrait',
        'instructions_speciales',
        'urgent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'montant_total' => 'decimal:2',
        'montant_assurance' => 'decimal:2',
        'montant_patient' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
        'latitude_livraison' => 'decimal:8',
        'longitude_livraison' => 'decimal:8',
        'date_commande' => 'datetime',
        'date_preparation' => 'datetime',
        'date_retrait_prevue' => 'datetime',
        'date_livraison_prevue' => 'datetime',
        'date_livraison_effective' => 'datetime',
        'urgent' => 'boolean',
    ];

    /**
     * Boot method for the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($commande) {
            if (!$commande->numero_commande) {
                $commande->numero_commande = $commande->genererNumeroCommande();
            }
            if (!$commande->date_commande) {
                $commande->date_commande = now();
            }
            if (!$commande->code_retrait) {
                $commande->code_retrait = strtoupper(Str::random(8));
            }
        });

        static::updating(function ($commande) {
            // Gérer les transitions de statut
            $commande->gererTransitionStatut();
        });
    }

    /**
     * Get the patient that owns the commande.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the pharmacie that owns the commande.
     */
    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    /**
     * Get the ordonnance associated with the commande.
     */
    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class);
    }

    /**
     * Get the lignes de commande.
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(LigneCommandePharma::class, 'commande_pharmaceutique_id');
    }

    /**
     * Get the livraison for the commande.
     */
    public function livraison(): HasOne
    {
        return $this->hasOne(LivraisonPharmaceutique::class, 'commande_pharmaceutique_id');
    }

    /**
     * Get the paiements for the commande.
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'reference_id')
            ->where('type_reference', 'commande_pharmaceutique');
    }

    /**
     * Scope pour les commandes urgentes
     */
    public function scopeUrgent($query)
    {
        return $query->where('urgent', true);
    }

    /**
     * Scope pour les commandes en cours
     */
    public function scopeEnCours($query)
    {
        return $query->whereIn('statut', ['confirmee', 'en_preparation', 'prete', 'en_livraison']);
    }

    /**
     * Scope pour les commandes terminées
     */
    public function scopeTerminees($query)
    {
        return $query->whereIn('statut', ['livree', 'annulee', 'remboursee']);
    }

    /**
     * Générer un numéro de commande unique
     */
    protected function genererNumeroCommande()
    {
        $prefix = 'CMD';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(6));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Gérer les transitions de statut
     */
    protected function gererTransitionStatut()
    {
        $ancienStatut = $this->getOriginal('statut');
        $nouveauStatut = $this->statut;

        // Définir les dates selon les transitions
        switch ($nouveauStatut) {
            case 'en_preparation':
                if (!$this->date_preparation) {
                    $this->date_preparation = now();
                }
                break;

            case 'prete':
                if ($this->mode_retrait === 'sur_place' && !$this->date_retrait_prevue) {
                    $this->date_retrait_prevue = now()->addHours(1);
                }
                break;

            case 'en_livraison':
                if (!$this->date_livraison_prevue) {
                    $this->date_livraison_prevue = now()->addHours(2);
                }
                break;

            case 'livree':
                if (!$this->date_livraison_effective) {
                    $this->date_livraison_effective = now();
                }
                break;
        }
    }

    /**
     * Vérifier si la commande peut être confirmée
     */
    public function peutEtreConfirmee()
    {
        return $this->statut === 'en_attente' &&
               $this->lignes()->count() > 0 &&
               $this->verifierStocksDisponibles();
    }

    /**
     * Vérifier la disponibilité des stocks
     */
    public function verifierStocksDisponibles()
    {
        foreach ($this->lignes as $ligne) {
            $stock = StockMedicament::where('pharmacie_id', $this->pharmacie_id)
                ->where('produit_pharmaceutique_id', $ligne->produit_pharmaceutique_id)
                ->first();

            if (!$stock || $stock->quantite_disponible < $ligne->quantite_commandee) {
                return false;
            }
        }

        return true;
    }

    /**
     * Confirmer la commande
     */
    public function confirmer()
    {
        if (!$this->peutEtreConfirmee()) {
            throw new \Exception('La commande ne peut pas être confirmée');
        }

        $this->statut = 'confirmee';
        $this->save();

        // Réserver les stocks
        $this->reserverStocks();

        // Créer la livraison si nécessaire
        if ($this->mode_retrait === 'livraison') {
            $this->creerLivraison();
        }

        return $this;
    }

    /**
     * Réserver les stocks pour la commande
     */
    protected function reserverStocks()
    {
        foreach ($this->lignes as $ligne) {
            $stock = StockMedicament::where('pharmacie_id', $this->pharmacie_id)
                ->where('produit_pharmaceutique_id', $ligne->produit_pharmaceutique_id)
                ->first();

            if ($stock) {
                $stock->retirerStock(
                    $ligne->quantite_commandee,
                    "Réservation pour commande {$this->numero_commande}",
                    $this->numero_commande
                );

                // Mettre à jour la ligne avec le stock utilisé
                $ligne->stock_medicament_id = $stock->id;
                $ligne->save();
            }
        }
    }

    /**
     * Créer une livraison pour la commande
     */
    protected function creerLivraison()
    {
        if (!$this->livraison) {
            return $this->livraison()->create([
                'numero_livraison' => 'LIV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'statut' => 'planifiee',
                'date_arrivee_prevue' => $this->date_livraison_prevue,
            ]);
        }

        return $this->livraison;
    }

    /**
     * Annuler la commande
     */
    public function annuler($motif = null)
    {
        if (in_array($this->statut, ['livree', 'annulee'])) {
            throw new \Exception('Cette commande ne peut pas être annulée');
        }

        // Libérer les stocks réservés
        if (in_array($this->statut, ['confirmee', 'en_preparation', 'prete'])) {
            $this->libererStocks();
        }

        $this->statut = 'annulee';
        $this->save();

        // Annuler la livraison si elle existe
        if ($this->livraison && !in_array($this->livraison->statut, ['livree'])) {
            $this->livraison->update([
                'statut' => 'echec',
                'motif_echec' => $motif ?? 'Commande annulée',
            ]);
        }

        return $this;
    }

    /**
     * Libérer les stocks réservés
     */
    protected function libererStocks()
    {
        foreach ($this->lignes as $ligne) {
            if ($ligne->stock_medicament_id) {
                $stock = StockMedicament::find($ligne->stock_medicament_id);
                if ($stock) {
                    $stock->ajouterStock(
                        $ligne->quantite_commandee,
                        "Annulation commande {$this->numero_commande}",
                        $this->numero_commande
                    );
                }
            }
        }
    }

    /**
     * Marquer comme prête
     */
    public function marquerPrete()
    {
        if (!in_array($this->statut, ['confirmee', 'en_preparation'])) {
            throw new \Exception('La commande ne peut pas être marquée comme prête');
        }

        $this->statut = 'prete';
        $this->save();

        // Notifier le patient
        $this->notifierPatient('Votre commande est prête',
            "Votre commande {$this->numero_commande} est prête. " .
            ($this->mode_retrait === 'sur_place' ?
                "Vous pouvez venir la récupérer avec le code: {$this->code_retrait}" :
                "Elle sera bientôt livrée à l'adresse indiquée.")
        );

        return $this;
    }

    /**
     * Marquer comme livrée
     */
    public function marquerLivree($signature = null, $photo = null)
    {
        if (!in_array($this->statut, ['prete', 'en_livraison'])) {
            throw new \Exception('La commande ne peut pas être marquée comme livrée');
        }

        $this->statut = 'livree';
        $this->date_livraison_effective = now();
        $this->save();

        // Mettre à jour la livraison
        if ($this->livraison) {
            $this->livraison->update([
                'statut' => 'livree',
                'date_livraison' => now(),
                'signature_receptionnaire' => $signature,
                'photo_livraison' => $photo,
            ]);
        }

        // Mettre à jour les quantités livrées
        foreach ($this->lignes as $ligne) {
            $ligne->quantite_livree = $ligne->quantite_commandee;
            $ligne->save();
        }

        return $this;
    }

    /**
     * Calculer le montant total
     */
    public function calculerMontantTotal()
    {
        $montant = $this->lignes->sum('montant_ligne');

        if ($this->mode_retrait === 'livraison') {
            $montant += $this->frais_livraison;
        }

        return $montant;
    }

    /**
     * Calculer le montant assurance
     */
    public function calculerMontantAssurance()
    {
        return $this->lignes->sum('montant_remboursement');
    }

    /**
     * Recalculer les montants
     */
    public function recalculerMontants()
    {
        $this->montant_total = $this->calculerMontantTotal();
        $this->montant_assurance = $this->calculerMontantAssurance();
        $this->montant_patient = $this->montant_total - $this->montant_assurance;
        $this->save();

        return $this;
    }

    /**
     * Vérifier si la commande est payée
     */
    public function estPayee()
    {
        $montantPaye = $this->paiements()
            ->where('statut', 'confirme')
            ->sum('montant');

        return $montantPaye >= $this->montant_patient;
    }

    /**
     * Obtenir le montant restant à payer
     */
    public function getMontantRestant()
    {
        $montantPaye = $this->paiements()
            ->where('statut', 'confirme')
            ->sum('montant');

        return max(0, $this->montant_patient - $montantPaye);
    }

    /**
     * Notifier le patient
     */
    protected function notifierPatient($titre, $message)
    {
        // Implémenter la notification (SMS, Email, Push)
        // Cette méthode sera connectée au système de notifications
    }

    /**
     * Obtenir le délai estimé
     */
    public function getDelaiEstime()
    {
        if ($this->mode_retrait === 'sur_place') {
            return $this->urgent ? '30 minutes' : '1 heure';
        } else {
            return $this->urgent ? '2 heures' : '4 heures';
        }
    }

    /**
     * Obtenir l'historique de statut
     */
    public function getHistoriqueStatut()
    {
        // À implémenter avec un système d'audit log
        return [];
    }
}
