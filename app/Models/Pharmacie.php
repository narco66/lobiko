<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacie extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pharmacies';

    protected $fillable = [
        'structure_medicale_id',
        'numero_licence',
        'nom_pharmacie',
        'nom_responsable',
        'telephone_pharmacie',
        'email_pharmacie',
        'adresse_complete',
        'latitude',
        'longitude',
        'horaires_ouverture',
        'service_garde',
        'livraison_disponible',
        'rayon_livraison_km',
        'frais_livraison_base',
        'frais_livraison_par_km',
        'paiement_mobile_money',
        'paiement_carte',
        'paiement_especes',
        'statut',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'horaires_ouverture' => 'array',
        'service_garde' => 'boolean',
        'livraison_disponible' => 'boolean',
        'rayon_livraison_km' => 'decimal:2',
        'frais_livraison_base' => 'decimal:2',
        'frais_livraison_par_km' => 'decimal:2',
        'paiement_mobile_money' => 'boolean',
        'paiement_carte' => 'boolean',
        'paiement_especes' => 'boolean',
    ];

    // Relations
    public function structureMedicale(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'structure_medicale_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(StockMedicament::class, 'pharmacie_id');
    }

    public function alertes(): HasMany
    {
        return $this->hasMany(AlerteStock::class, 'pharmacie_id');
    }

    public function alertesNonTraitees(): HasMany
    {
        return $this->alertes()->where('traitee', false);
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(CommandePharmaceutique::class, 'pharmacie_id');
    }

    public function fournisseurs(): BelongsToMany
    {
        return $this->belongsToMany(FournisseurPharmaceutique::class, 'pharmacie_fournisseur')
            ->withPivot(['numero_compte_client', 'statut', 'credit_maximum', 'credit_utilise'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('statut', 'active');
    }

    // Métiers
    public function calculerFraisLivraison(?float $latitude, ?float $longitude): ?float
    {
        if (!$this->livraison_disponible || !$latitude || !$longitude || !$this->latitude || !$this->longitude) {
            return null;
        }

        $distance = $this->calculerDistance(
            (float) $this->latitude,
            (float) $this->longitude,
            $latitude,
            $longitude
        );

        if ($this->rayon_livraison_km && $distance > (float) $this->rayon_livraison_km) {
            return null;
        }

        $base = (float) ($this->frais_livraison_base ?? 0);
        $parKm = (float) ($this->frais_livraison_par_km ?? 0);

        return round($base + ($distance * $parKm), 2);
    }

    public function getStatistiques(): array
    {
        return [
            'stocks' => $this->stocks()->count(),
            'alertes_actives' => $this->alertes()->where('traitee', false)->count(),
            'commandes_total' => $this->commandes()->count(),
            'commandes_livrees' => $this->commandes()->where('statut', 'livree')->count(),
        ];
    }

    // Utilitaires
    protected function calculerDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDiff / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
