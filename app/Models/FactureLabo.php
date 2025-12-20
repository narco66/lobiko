<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactureLabo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'factures_labo';

    protected $fillable = [
        'rendez_vous_labo_id',
        'montant_examen',
        'frais_deplacement',
        'commission_lobiko',
        'montant_total',
        'statut_paiement',
        'mode_paiement',
    ];

    protected $casts = [
        'montant_examen' => 'decimal:2',
        'frais_deplacement' => 'decimal:2',
        'commission_lobiko' => 'decimal:2',
        'montant_total' => 'decimal:2',
    ];

    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVousLabo::class, 'rendez_vous_labo_id');
    }
}
