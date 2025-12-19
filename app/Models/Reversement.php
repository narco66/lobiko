<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reversement extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'numero_reversement',
        'beneficiaire_id',
        'type_beneficiaire',
        'periode_debut',
        'periode_fin',
        'mois_annee',
        'montant_brut',
        'commission_plateforme',
        'taux_commission',
        'retenues_fiscales',
        'autres_retenues',
        'montant_net',
        'nombre_consultations',
        'nombre_actes',
        'detail_consultations',
        'detail_retenues',
        'mode_paiement',
        'compte_beneficiaire',
        'reference_paiement',
        'statut',
        'date_calcul',
        'date_validation',
        'date_paiement_prevu',
        'date_paiement_effectif',
    ];

    protected $casts = [
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'date_calcul' => 'date',
        'date_validation' => 'date',
        'date_paiement_prevu' => 'date',
        'date_paiement_effectif' => 'date',
        'detail_consultations' => 'array',
        'detail_retenues' => 'array',
        'montant_brut' => 'decimal:2',
        'commission_plateforme' => 'decimal:2',
        'retenues_fiscales' => 'decimal:2',
        'autres_retenues' => 'decimal:2',
        'montant_net' => 'decimal:2',
    ];

    public static function generateNumero(): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $count = static::whereYear('created_at', $year)->whereMonth('created_at', now()->month)->count() + 1;
        $num = str_pad($count, 5, '0', STR_PAD_LEFT);
        return "REV-{$year}{$month}-{$num}";
    }
}
