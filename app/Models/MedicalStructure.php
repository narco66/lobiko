<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MedicalStructure extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'structures_medicales';

    protected $fillable = [
        'code_structure',
        'nom_structure',
        'type_structure',
        'numero_agrement',
        'numero_fiscal',
        'registre_commerce',
        'adresse_rue',
        'adresse_quartier',
        'adresse_ville',
        'adresse_pays',
        'latitude',
        'longitude',
        'telephone_principal',
        'telephone_secondaire',
        'email',
        'site_web',
        'horaires_ouverture',
        'urgences_24h',
        'garde_weekend',
        'responsable_id',
        'services_disponibles',
        'equipements',
        'nombre_lits',
        'nombre_salles',
        'parking_disponible',
        'accessible_handicapes',
        'assurances_acceptees',
        'tiers_payant',
        'categorie_tarif',
        'taux_majoration',
        'statut',
        'verified',
        'verified_at',
        'verified_by',
        'logo',
        'photo_facade',
        'galerie_photos',
        'document_agrement',
        'note_moyenne',
        'nombre_evaluations',
        'nombre_consultations',
        'compte_bancaire',
        'code_banque',
        'iban',
        'commission_plateforme',
    ];

    protected $casts = [
        'horaires_ouverture' => 'array',
        'services_disponibles' => 'array',
        'equipements' => 'array',
        'assurances_acceptees' => 'array',
        'galerie_photos' => 'array',
        'urgences_24h' => 'boolean',
        'garde_weekend' => 'boolean',
        'parking_disponible' => 'boolean',
        'accessible_handicapes' => 'boolean',
        'tiers_payant' => 'boolean',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'note_moyenne' => 'decimal:2',
        'taux_majoration' => 'decimal:2',
        'commission_plateforme' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_structure', 'structure_id', 'doctor_id')
            ->withPivot(['role', 'actif', 'date_debut', 'date_fin', 'pourcentage_honoraires'])
            ->withTimestamps();
    }

    public function openingHours()
    {
        return $this->hasMany(StructureOpeningHour::class, 'structure_id');
    }

    public function scopeActives($query)
    {
        return $query->where('statut', 'actif');
    }
}
