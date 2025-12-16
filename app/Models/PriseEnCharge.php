<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PriseEnCharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prises_en_charge';

    protected $fillable = [
        'numero_pec',
        'contrat_id',
        'facture_id',
        'devis_id',
        'patient_id',
        'praticien_id',
        'structure_id',
        'type_pec',
        'montant_demande',
        'montant_accorde',
        'taux_pec',
        'motif',
        'statut',
        'date_demande',
        'date_reponse',
        'validite_jours',
        'date_expiration',
        'justificatifs',
        'commentaire_assurance',
        'metadata'
    ];

    protected $casts = [
        'montant_demande' => 'decimal:2',
        'montant_accorde' => 'decimal:2',
        'taux_pec' => 'decimal:2',
        'validite_jours' => 'integer',
        'date_demande' => 'datetime',
        'date_reponse' => 'datetime',
        'date_expiration' => 'date',
        'justificatifs' => 'array',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pec) {
            if (empty($pec->numero_pec)) {
                $pec->numero_pec = self::generateNumeroPec();
            }
            if (empty($pec->date_demande)) {
                $pec->date_demande = now();
            }
            if ($pec->validite_jours && empty($pec->date_expiration)) {
                $pec->date_expiration = now()->addDays($pec->validite_jours);
            }
        });
    }

    // Relations
    public function contrat(): BelongsTo
    {
        return $this->belongsTo(ContratAssurance::class, 'contrat_id');
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'facture_id');
    }

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class, 'devis_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function praticien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'praticien_id');
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'structure_id');
    }

    public function litiges(): HasMany
    {
        return $this->hasMany(Litige::class, 'pec_id');
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeAcceptees($query)
    {
        return $query->where('statut', 'acceptee');
    }

    public function scopeRefusees($query)
    {
        return $query->where('statut', 'refusee');
    }

    public function scopeExpirees($query)
    {
        return $query->where('statut', 'expiree')
                    ->orWhere(function($q) {
                        $q->where('date_expiration', '<', now())
                          ->where('statut', '!=', 'utilisee');
                    });
    }

    public function scopeValides($query)
    {
        return $query->where('statut', 'acceptee')
                    ->where(function($q) {
                        $q->whereNull('date_expiration')
                          ->orWhere('date_expiration', '>=', now());
                    });
    }

    // Méthodes métier
    public static function generateNumeroPec(): string
    {
        $prefix = 'PEC';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(Str::random(6));

        return "{$prefix}-{$year}{$month}-{$random}";
    }

    public function estValide(): bool
    {
        return $this->statut === 'acceptee'
            && ($this->date_expiration === null || $this->date_expiration >= now());
    }

    public function estExpiree(): bool
    {
        return $this->date_expiration !== null && $this->date_expiration < now();
    }

    public function accepter(float $montantAccorde = null, string $commentaire = null): void
    {
        $this->statut = 'acceptee';
        $this->date_reponse = now();
        $this->montant_accorde = $montantAccorde ?? $this->montant_demande;

        if ($commentaire) {
            $this->commentaire_assurance = $commentaire;
        }

        // Calculer le taux de PEC
        if ($this->montant_demande > 0) {
            $this->taux_pec = ($this->montant_accorde / $this->montant_demande) * 100;
        }

        $this->save();

        // Créer une notification pour le patient
        Notification::create([
            'user_id' => $this->patient_id,
            'type' => 'pec_acceptee',
            'titre' => 'Prise en charge acceptée',
            'message' => "Votre demande de prise en charge {$this->numero_pec} a été acceptée",
            'data' => ['pec_id' => $this->id]
        ]);
    }

    public function refuser(string $motifRefus): void
    {
        $this->statut = 'refusee';
        $this->date_reponse = now();
        $this->montant_accorde = 0;
        $this->commentaire_assurance = $motifRefus;
        $this->save();

        // Créer une notification pour le patient
        Notification::create([
            'user_id' => $this->patient_id,
            'type' => 'pec_refusee',
            'titre' => 'Prise en charge refusée',
            'message' => "Votre demande de prise en charge {$this->numero_pec} a été refusée",
            'data' => ['pec_id' => $this->id, 'motif' => $motifRefus]
        ]);
    }

    public function marquerUtilisee(): void
    {
        if ($this->statut === 'acceptee') {
            $this->statut = 'utilisee';
            $this->save();

            // Consommer le plafond du contrat
            if ($this->contrat) {
                $this->contrat->consommerPlafond($this->montant_accorde);
            }
        }
    }

    public function annuler(string $motif = null): void
    {
        $this->statut = 'annulee';

        if ($motif) {
            $metadata = $this->metadata ?? [];
            $metadata['motif_annulation'] = $motif;
            $metadata['date_annulation'] = now()->toDateTimeString();
            $this->metadata = $metadata;
        }

        $this->save();
    }

    // Méthodes de calcul
    public function calculerResteACharge(): float
    {
        return max(0, $this->montant_demande - $this->montant_accorde);
    }

    public function getTauxCouvertureAttribute(): float
    {
        if ($this->montant_demande == 0) {
            return 0;
        }
        return round(($this->montant_accorde / $this->montant_demande) * 100, 2);
    }

    // Méthodes de formatage
    public function getStatutBadgeAttribute(): string
    {
        $badges = [
            'en_attente' => '<span class="badge bg-warning">En attente</span>',
            'acceptee' => '<span class="badge bg-success">Acceptée</span>',
            'refusee' => '<span class="badge bg-danger">Refusée</span>',
            'utilisee' => '<span class="badge bg-info">Utilisée</span>',
            'expiree' => '<span class="badge bg-secondary">Expirée</span>',
            'annulee' => '<span class="badge bg-dark">Annulée</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getDelaiTraitementAttribute(): ?string
    {
        if (!$this->date_reponse) {
            return null;
        }

        $delai = $this->date_demande->diffInDays($this->date_reponse);

        if ($delai == 0) {
            return 'Traité le jour même';
        } elseif ($delai == 1) {
            return '1 jour';
        } else {
            return "{$delai} jours";
        }
    }
}
