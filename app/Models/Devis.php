<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'numero_devis',
        'patient_id',
        'praticien_id',
        'structure_id',
        'consultation_id',
        'rendez_vous_id',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'montant_remise',
        'montant_majoration',
        'montant_final',
        'contrat_assurance_id',
        'montant_assurance',
        'reste_a_charge',
        'simulation_pec',
        'detail_couverture',
        'date_emission',
        'date_validite',
        'duree_validite',
        'statut',
        'accepte_patient',
        'accepte_patient_at',
        'signature_patient',
        'motif_refus',
        'converti_facture',
        'facture_id',
        'converti_at',
        'devis_pdf',
        'notes_internes',
        'conditions_particulieres',
        'mentions_legales',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_validite' => 'date',
        'accepte_patient_at' => 'datetime',
        'converti_at' => 'datetime',
        'detail_couverture' => 'array',
        'simulation_pec' => 'boolean',
        'accepte_patient' => 'boolean',
        'converti_facture' => 'boolean',
        'montant_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'montant_remise' => 'decimal:2',
        'montant_majoration' => 'decimal:2',
        'montant_final' => 'decimal:2',
        'montant_assurance' => 'decimal:2',
        'reste_a_charge' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Devis $devis) {
            if (!$devis->numero_devis) {
                $devis->numero_devis = self::generateNumero();
            }
            if (!$devis->date_emission) {
                $devis->date_emission = now();
            }
            if (!$devis->date_validite) {
                $days = $devis->duree_validite ?? 30;
                $devis->date_validite = ($devis->date_emission ?? now())->copy()->addDays($days);
            }
            $devis->montant_ttc = $devis->montant_ttc ?? $devis->montant_final;
            $devis->reste_a_charge = $devis->reste_a_charge ?? $devis->montant_final;
        });
    }

    public static function generateNumero(): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $lastNumber = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        $number = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);

        return "DEV-{$year}{$month}-{$number}";
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function praticien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'praticien_id');
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructureMedicale::class, 'structure_id');
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'facture_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(DevisLigne::class, 'devis_id');
    }
}
