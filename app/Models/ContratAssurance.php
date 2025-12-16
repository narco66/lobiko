<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContratAssurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contrats_assurance';

    protected $fillable = [
        'patient_id',
        'assurance_id',
        'numero_contrat',
        'type_contrat',
        'taux_couverture',
        'plafond_annuel',
        'plafond_consomme',
        'exclusions',
        'date_debut',
        'date_fin',
        'statut',
        'documents',
        'metadata'
    ];

    protected $casts = [
        'taux_couverture' => 'decimal:2',
        'plafond_annuel' => 'decimal:2',
        'plafond_consomme' => 'decimal:2',
        'exclusions' => 'array',
        'documents' => 'array',
        'metadata' => 'array',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // Relations
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function assurance(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assurance_id');
    }

    public function prisesEnCharge(): HasMany
    {
        return $this->hasMany(PriseEnCharge::class, 'contrat_id');
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif')
                    ->where('date_debut', '<=', now())
                    ->where('date_fin', '>=', now());
    }

    public function scopePatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    // Méthodes métier
    public function estValide(): bool
    {
        return $this->statut === 'actif'
            && $this->date_debut <= now()
            && $this->date_fin >= now();
    }

    public function tauxRestant(): float
    {
        if ($this->plafond_annuel == 0) {
            return 100;
        }
        return (($this->plafond_annuel - $this->plafond_consomme) / $this->plafond_annuel) * 100;
    }

    public function montantRestant(): float
    {
        return $this->plafond_annuel - $this->plafond_consomme;
    }

    public function peutCouvrir(float $montant): bool
    {
        $montantCouvert = $montant * ($this->taux_couverture / 100);
        return $this->montantRestant() >= $montantCouvert;
    }

    public function calculerCouverture(float $montant, string $typeActe = null): array
    {
        // Vérifier les exclusions
        if ($typeActe && in_array($typeActe, $this->exclusions ?? [])) {
            return [
                'montant_couvert' => 0,
                'reste_a_charge' => $montant,
                'raison' => 'Acte exclu du contrat'
            ];
        }

        // Calculer le montant couvert
        $montantCouvert = min(
            $montant * ($this->taux_couverture / 100),
            $this->montantRestant()
        );

        return [
            'montant_couvert' => round($montantCouvert, 2),
            'reste_a_charge' => round($montant - $montantCouvert, 2),
            'taux_applique' => $this->taux_couverture,
            'plafond_restant' => $this->montantRestant()
        ];
    }

    public function consommerPlafond(float $montant): void
    {
        $this->plafond_consomme += $montant;
        $this->save();
    }

    // Méthodes de formatage
    public function getNumeroFormatteAttribute(): string
    {
        return strtoupper($this->numero_contrat);
    }

    public function getStatutBadgeAttribute(): string
    {
        $badges = [
            'actif' => '<span class="badge bg-success">Actif</span>',
            'suspendu' => '<span class="badge bg-warning">Suspendu</span>',
            'expire' => '<span class="badge bg-danger">Expiré</span>',
            'annule' => '<span class="badge bg-dark">Annulé</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }
}
