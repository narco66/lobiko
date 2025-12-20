<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Examen extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'laboratoire_id',
        'nom',
        'code',
        'description',
        'tarif_base',
        'tarifs_personnalises',
        'delai_resultat',
        'statut',
    ];

    protected $casts = [
        'tarif_base' => 'decimal:2',
        'tarifs_personnalises' => 'array',
    ];

    public function laboratoire(): BelongsTo
    {
        return $this->belongsTo(Laboratoire::class);
    }
}
