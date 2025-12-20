<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RendezVousLabo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rendez_vous_labo';

    protected $fillable = [
        'laboratoire_id',
        'patient_id',
        'examen_id',
        'mode',
        'date_rdv',
        'creneau',
        'adresse',
        'latitude',
        'longitude',
        'statut',
    ];

    protected $casts = [
        'date_rdv' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function laboratoire(): BelongsTo
    {
        return $this->belongsTo(Laboratoire::class);
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }

    public function prelevement(): HasOne
    {
        return $this->hasOne(PrelevementDomicile::class, 'rendez_vous_labo_id');
    }

    public function facture(): HasOne
    {
        return $this->hasOne(FactureLabo::class, 'rendez_vous_labo_id');
    }

    public function resultat(): HasOne
    {
        return $this->hasOne(ResultatLabo::class, 'rendez_vous_labo_id');
    }
}
