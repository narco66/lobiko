<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Doctor extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'user_id',
        'matricule',
        'nom',
        'prenom',
        'telephone',
        'email',
        'specialty_id',
        'statut',
        'verified',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty');
    }

    public function structures()
    {
        return $this->belongsToMany(MedicalStructure::class, 'doctor_structure', 'doctor_id', 'structure_id')
            ->withPivot(['role', 'actif', 'date_debut', 'date_fin', 'pourcentage_honoraires'])
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function absences()
    {
        return $this->hasMany(DoctorAbsence::class);
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
