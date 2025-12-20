<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultatLabo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'resultats_labo';

    protected $fillable = [
        'rendez_vous_labo_id',
        'fichier',
        'commentaire',
        'publie_par',
        'publie_at',
    ];

    protected $casts = [
        'publie_at' => 'datetime',
    ];

    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVousLabo::class, 'rendez_vous_labo_id');
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publie_par');
    }
}
