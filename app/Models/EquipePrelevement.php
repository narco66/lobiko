<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipePrelevement extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'equipes_prelevement';

    protected $fillable = [
        'laboratoire_id',
        'nom',
        'telephone',
        'statut',
    ];

    public function laboratoire(): BelongsTo
    {
        return $this->belongsTo(Laboratoire::class);
    }

    public function prelevements(): HasMany
    {
        return $this->hasMany(PrelevementDomicile::class, 'equipe_id');
    }
}
