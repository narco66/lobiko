<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrelevementDomicile extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'prelevements_domicile';

    protected $fillable = [
        'rendez_vous_labo_id',
        'equipe_id',
        'statut',
        'date_programmee',
        'creneau',
        'frais_deplacement',
    ];

    protected $casts = [
        'date_programmee' => 'datetime',
        'frais_deplacement' => 'decimal:2',
    ];

    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVousLabo::class, 'rendez_vous_labo_id');
    }

    public function equipe(): BelongsTo
    {
        return $this->belongsTo(EquipePrelevement::class, 'equipe_id');
    }
}
