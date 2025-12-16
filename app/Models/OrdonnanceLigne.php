<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdonnanceLigne extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ordonnance_lignes';

    protected $fillable = [
        'ordonnance_id',
        'produit_id',
        'dci',
        'nom_commercial',
        'dosage',
        'forme',
        'quantite',
        'posologie',
        'duree_traitement',
        'unite_duree',
        'voie_administration',
        'moment_prise',
        'instructions_speciales',
        'substitution_autorisee',
        'urgence',
        'dispensee',
        'quantite_dispensee',
        'date_dispensation',
        'pharmacie_id',
        'prix_unitaire',
        'montant_total',
        'metadata'
    ];

    protected $casts = [
        'quantite' => 'integer',
        'duree_traitement' => 'integer',
        'substitution_autorisee' => 'boolean',
        'urgence' => 'boolean',
        'dispensee' => 'boolean',
        'quantite_dispensee' => 'integer',
        'date_dispensation' => 'datetime',
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'moment_prise' => 'array',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ligne) {
            // Calculer le montant total
            if ($ligne->quantite && $ligne->prix_unitaire) {
                $ligne->montant_total = $ligne->quantite * $ligne->prix_unitaire;
            }
        });
    }

    // Relations
    public function ordonnance(): BelongsTo
    {
        return $this->belongsTo(Ordonnance::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(ProduitPharmaceutique::class, 'produit_id');
    }

    public function pharmacieDispensatrice(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'pharmacie_id');
    }

    // Scopes
    public function scopeDispensees($query)
    {
        return $query->where('dispensee', true);
    }

    public function scopeNonDispensees($query)
    {
        return $query->where('dispensee', false);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('urgence', true);
    }

    public function scopeSubstituables($query)
    {
        return $query->where('substitution_autorisee', true);
    }

    // Méthodes métier
    public function marquerDispensee(int $quantiteDispensee = null, int $pharmacieId = null): void
    {
        $this->dispensee = true;
        $this->quantite_dispensee = $quantiteDispensee ?? $this->quantite;
        $this->date_dispensation = now();

        if ($pharmacieId) {
            $this->pharmacie_id = $pharmacieId;
        }

        $this->save();
    }

    public function estCompletementDispensee(): bool
    {
        return $this->dispensee && $this->quantite_dispensee >= $this->quantite;
    }

    public function estPartiellementDispensee(): bool
    {
        return $this->dispensee && $this->quantite_dispensee < $this->quantite;
    }

    public function getQuantiteRestanteAttribute(): int
    {
        if (!$this->dispensee) {
            return $this->quantite;
        }

        return max(0, $this->quantite - $this->quantite_dispensee);
    }

    public function substituer(ProduitPharmaceutique $nouveauProduit): void
    {
        if (!$this->substitution_autorisee) {
            throw new \Exception("La substitution n'est pas autorisée pour ce médicament");
        }

        $metadata = $this->metadata ?? [];
        $metadata['produit_original'] = [
            'id' => $this->produit_id,
            'nom' => $this->nom_commercial,
            'dci' => $this->dci,
            'dosage' => $this->dosage
        ];
        $metadata['date_substitution'] = now()->toDateTimeString();

        $this->produit_id = $nouveauProduit->id;
        $this->nom_commercial = $nouveauProduit->nom_commercial;
        $this->dci = $nouveauProduit->dci;
        $this->dosage = $nouveauProduit->dosage;
        $this->prix_unitaire = $nouveauProduit->prix_unitaire;
        $this->metadata = $metadata;

        $this->save();
    }

    // Méthodes de formatage de la posologie
    public function getPosologieCompleteAttribute(): string
    {
        $posologie = $this->posologie;

        if ($this->duree_traitement) {
            $unite = $this->unite_duree ?? 'jour(s)';
            $posologie .= " pendant {$this->duree_traitement} {$unite}";
        }

        if ($this->voie_administration) {
            $posologie .= " - Voie: {$this->voie_administration}";
        }

        if ($this->moment_prise && is_array($this->moment_prise)) {
            $moments = implode(', ', $this->moment_prise);
            $posologie .= " - Moments: {$moments}";
        }

        if ($this->instructions_speciales) {
            $posologie .= " - Instructions: {$this->instructions_speciales}";
        }

        return $posologie;
    }

    public function getPosologieSimplifieeAttribute(): string
    {
        return $this->posologie ?? 'Selon prescription';
    }

    // Méthodes de calcul de durée
    public function calculerDateFinTraitement(): ?string
    {
        if (!$this->duree_traitement) {
            return null;
        }

        $dateDebut = $this->date_dispensation ?? now();
        $unite = strtolower($this->unite_duree ?? 'jour');

        switch ($unite) {
            case 'jour':
            case 'jours':
                return $dateDebut->addDays($this->duree_traitement)->format('d/m/Y');
            case 'semaine':
            case 'semaines':
                return $dateDebut->addWeeks($this->duree_traitement)->format('d/m/Y');
            case 'mois':
                return $dateDebut->addMonths($this->duree_traitement)->format('d/m/Y');
            default:
                return $dateDebut->addDays($this->duree_traitement)->format('d/m/Y');
        }
    }

    // Méthodes de validation
    public function verifierDisponibilite(): bool
    {
        if (!$this->produit) {
            return false;
        }

        return $this->produit->stock_disponible >= $this->quantite;
    }

    public function trouverSubstituts(): array
    {
        if (!$this->dci) {
            return [];
        }

        return ProduitPharmaceutique::where('dci', $this->dci)
            ->where('dosage', $this->dosage)
            ->where('id', '!=', $this->produit_id)
            ->where('stock_disponible', '>=', $this->quantite)
            ->get()
            ->toArray();
    }

    // Méthodes de formatage
    public function getStatutDispensationAttribute(): string
    {
        if (!$this->dispensee) {
            return '<span class="badge bg-secondary">Non dispensé</span>';
        }

        if ($this->estCompletementDispensee()) {
            return '<span class="badge bg-success">Dispensé</span>';
        }

        return '<span class="badge bg-warning">Partiellement dispensé</span>';
    }

    public function getUrgenceBadgeAttribute(): string
    {
        if ($this->urgence) {
            return '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Urgent</span>';
        }

        return '';
    }

    public function getSubstitutionBadgeAttribute(): string
    {
        if ($this->substitution_autorisee) {
            return '<span class="badge bg-info">Substitution autorisée</span>';
        }

        return '<span class="badge bg-secondary">Non substituable</span>';
    }
}
