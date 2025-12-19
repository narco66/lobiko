<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'website',
        'description',
        'type',
        'partner_type',
        'statut',
        'commission_mode',
        'commission_value',
        'contact_email',
        'contact_phone',
        'adresse_ville',
        'adresse_pays',
        'numero_legal',
        'order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function conventionsAssureur()
    {
        return $this->hasMany(Convention::class, 'assureur_partner_id');
    }

    public function conventionsPrestataire()
    {
        return $this->hasMany(Convention::class, 'prestataire_partner_id');
    }
}
