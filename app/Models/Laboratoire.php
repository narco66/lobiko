<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laboratoire extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nom',
        'responsable',
        'telephone',
        'email',
        'adresse',
        'latitude',
        'longitude',
        'ville',
        'pays',
        'statut',
        'rayon_couverture_km',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rayon_couverture_km' => 'decimal:2',
    ];

    public function examens(): HasMany
    {
        return $this->hasMany(Examen::class);
    }

    public function equipes(): HasMany
    {
        return $this->hasMany(EquipePrelevement::class, 'laboratoire_id');
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVousLabo::class, 'laboratoire_id');
    }
}
