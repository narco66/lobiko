<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMedicament extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'stocks_medicaments';

    protected $fillable = [
        'pharmacie_id',
        'produit_pharmaceutique_id',
        'quantite_disponible',
        'quantite_minimum',
        'quantite_maximum',
        'prix_vente',
        'prix_achat',
        'numero_lot',
        'date_expiration',
        'emplacement_rayon',
        'prescription_requise',
        'disponible_vente',
        'statut_stock',
    ];

    protected $casts = [
        'quantite_disponible' => 'integer',
        'quantite_minimum' => 'integer',
        'quantite_maximum' => 'integer',
        'prix_vente' => 'decimal:2',
        'prix_achat' => 'decimal:2',
        'date_expiration' => 'date',
        'prescription_requise' => 'boolean',
        'disponible_vente' => 'boolean',
    ];

    // Relations
    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class, 'pharmacie_id');
    }

    public function produitPharmaceutique(): BelongsTo
    {
        return $this->belongsTo(ProduitPharmaceutique::class, 'produit_pharmaceutique_id');
    }

    public function alertes(): HasMany
    {
        return $this->hasMany(AlerteStock::class, 'stock_medicament_id');
    }

    // Scopes
    public function scopeDisponible($query)
    {
        return $query->where('disponible_vente', true)
            ->where('quantite_disponible', '>', 0)
            ->where('statut_stock', '!=', 'expire');
    }

    public function scopeStockFaible($query)
    {
        return $query->whereColumn('quantite_disponible', '<=', 'quantite_minimum');
    }

    public function scopeExpirationProche($query, int $days = 30)
    {
        return $query->whereNotNull('date_expiration')
            ->where('date_expiration', '<=', now()->addDays($days));
    }

    public function scopeExpire($query)
    {
        return $query->whereNotNull('date_expiration')
            ->where('date_expiration', '<', now());
    }

    // Métiers stock
    public function ajouterStock(int $quantite, ?string $motif = null): void
    {
        $avant = $this->quantite_disponible;
        $this->quantite_disponible += $quantite;
        $this->save();

        $this->logMouvement('entree', $quantite, $avant, $this->quantite_disponible, $motif);
    }

    public function retirerStock(int $quantite, ?string $motif = null, ?string $reference = null): void
    {
        if ($quantite <= 0) {
            return;
        }

        if ($this->quantite_disponible < $quantite) {
            throw new \Exception("Stock insuffisant pour {$this->produitPharmaceutique->nom_commercial}");
        }

        $avant = $this->quantite_disponible;
        $this->quantite_disponible -= $quantite;
        $this->save();

        $this->logMouvement('sortie', $quantite, $avant, $this->quantite_disponible, $motif, $reference);
    }

    public function ajusterStock(int $nouvelleQuantite, string $motif): void
    {
        $avant = $this->quantite_disponible;
        $this->quantite_disponible = max(0, $nouvelleQuantite);
        $this->save();

        $type = $nouvelleQuantite >= $avant ? 'entree' : 'ajustement';
        $difference = abs($nouvelleQuantite - $avant);

        $this->logMouvement($type, $difference, $avant, $this->quantite_disponible, $motif);
    }

    public function necessiteAlerte(): array
    {
        $alertes = [];

        if ($this->quantite_disponible <= $this->quantite_minimum) {
            $alertes[] = 'stock_faible';
        }

        if ($this->quantite_disponible === 0) {
            $alertes[] = 'rupture_stock';
        }

        if ($this->date_expiration && $this->date_expiration < now()->addDays(30)) {
            $alertes[] = $this->date_expiration < now() ? 'expire' : 'expiration_proche';
        }

        return $alertes;
    }

    public function creerAlertes(): void
    {
        foreach ($this->necessiteAlerte() as $type) {
            $this->alertes()->create([
                'pharmacie_id' => $this->pharmacie_id,
                'type_alerte' => $type,
                'message' => $this->messageAlerte($type),
            ]);
        }
    }

    // Utilitaires
    protected function messageAlerte(string $type): string
    {
        $nom = $this->produitPharmaceutique?->nom_commercial ?? 'Produit';
        return match ($type) {
            'stock_faible' => "Stock faible pour {$nom}",
            'rupture_stock' => "Rupture de stock pour {$nom}",
            'expiration_proche' => "Expiration proche pour {$nom} - Lot {$this->numero_lot}",
            'expire' => "Produit expiré {$nom} - Lot {$this->numero_lot}",
            default => "Alerte stock pour {$nom}",
        };
    }

    protected function logMouvement(string $type, int $quantite, int $avant, int $apres, ?string $motif = null, ?string $reference = null): void
    {
        DB::table('mouvements_stock')->insert([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'stock_medicament_id' => $this->id,
            'utilisateur_id' => Auth::id(),
            'type_mouvement' => $type,
            'quantite' => $quantite,
            'stock_avant' => $avant,
            'stock_apres' => $apres,
            'reference_document' => $reference,
            'motif' => $motif,
            'prix_unitaire' => $this->prix_vente,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
