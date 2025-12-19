<?php

namespace App\Models;

use App\Models\CommandePharmaceutique;
use App\Models\Facture;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Paiement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const STATUTS = [
        'initie',
        'en_cours',
        'confirme',
        'echoue',
        'annule',
        'rembourse',
        'timeout',
    ];

    protected $fillable = [
        'numero_paiement',
        'facture_id',
        'payeur_id',
        'commande_id',
        'reference_id',
        'type_reference',
        'type_payeur',
        'mode_paiement',
        'montant',
        'devise',
        'taux_change',
        'montant_devise_locale',
        'frais_transaction',
        'montant_net',
        'montant_pharmacie',
        'montant_livreur',
        'commission_plateforme',
        'reference_transaction',
        'reference_passerelle',
        'statut',
        'statut_cantonnement',
        'reversement_genere',
        'reversement_pharmacie_id',
        'reversement_livreur_id',
        'payout_tagged_at',
        'date_cantonnement',
        'date_liberation',
        'idempotence_key',
        'tentatives',
        'derniere_tentative',
        'passerelle',
        'reponse_passerelle',
        'code_autorisation',
        'code_erreur',
        'message_erreur',
        'date_initiation',
        'date_confirmation',
        'date_annulation',
        'remboursable',
        'montant_rembourse',
        'date_remboursement',
        'reference_remboursement',
        'valide',
        'valide_par',
        'valide_at',
        'agent_id',
        'code_agent',
        'lieu_paiement',
        'recu_pdf',
        'preuve_paiement',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'montant_devise_locale' => 'decimal:2',
        'taux_change' => 'decimal:4',
        'frais_transaction' => 'decimal:2',
        'montant_net' => 'decimal:2',
        'montant_pharmacie' => 'decimal:2',
        'montant_livreur' => 'decimal:2',
        'commission_plateforme' => 'decimal:2',
        'remboursable' => 'boolean',
        'valide' => 'boolean',
        'reponse_passerelle' => 'array',
        'date_initiation' => 'datetime',
        'date_confirmation' => 'datetime',
        'date_annulation' => 'datetime',
        'date_remboursement' => 'datetime',
        'valide_at' => 'datetime',
        'derniere_tentative' => 'datetime',
        'date_cantonnement' => 'datetime',
        'date_liberation' => 'datetime',
        'tentatives' => 'integer',
        'reversement_genere' => 'boolean',
        'payout_tagged_at' => 'datetime',
    ];

    public static function generateNumero(): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $lastNumber = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        $number = str_pad($lastNumber, 5, '0', STR_PAD_LEFT);

        return "PAY-{$year}{$month}-{$number}";
    }

    // Relations
    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function payeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payeur_id');
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(CommandePharmaceutique::class, 'commande_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'type_reference', 'reference_id');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('statut', 'confirme');
    }

    public function scopePending($query)
    {
        return $query->whereIn('statut', ['initie', 'en_cours', 'timeout']);
    }

    // Métier
    public function markAsConfirmed(?User $validator = null): void
    {
        if ($this->statut === 'confirme') {
            return;
        }

        $this->update([
            'statut' => 'confirme',
            'date_confirmation' => now(),
            'valide' => true,
            'valide_par' => $validator?->id,
            'valide_at' => $validator ? now() : null,
        ]);

        if ($this->facture) {
            $this->facture->updateRemainingAmount();
        }
    }

    protected function montantNet(): Attribute
    {
        return Attribute::make(
            set: function ($value, array $attributes) {
                if ($value !== null) {
                    return $value;
                }

                $montant = $attributes['montant'] ?? 0;
                $fees = $attributes['frais_transaction'] ?? 0;

                return max($montant - $fees, 0);
            }
        );
    }

    protected function numeroPaiement(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ?: self::generateNumero()
        );
    }
}
