<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use App\Models\Dispensation;

class Ordonnance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_ordonnance',
        'consultation_id',
        'patient_id',
        'praticien_id',
        'structure_id',
        'date_ordonnance',
        'validite_jours',
        'date_expiration',
        'diagnostic',
        'observations',
        'signature_numerique',
        'qr_code',
        'statut',
        'type_ordonnance',
        'renouvelable',
        'nombre_renouvellements',
        'renouvellements_effectues',
        'metadata'
    ];

    protected $casts = [
        'date_ordonnance' => 'datetime',
        'date_expiration' => 'date',
        'validite_jours' => 'integer',
        'renouvelable' => 'boolean',
        'nombre_renouvellements' => 'integer',
        'renouvellements_effectues' => 'integer',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ordonnance) {
            if (empty($ordonnance->numero_ordonnance)) {
                $ordonnance->numero_ordonnance = self::generateNumeroOrdonnance();
            }

            if (empty($ordonnance->date_ordonnance)) {
                $ordonnance->date_ordonnance = now();
            }

            // Calculer la date d'expiration
            if ($ordonnance->validite_jours && empty($ordonnance->date_expiration)) {
                $ordonnance->date_expiration = now()->addDays($ordonnance->validite_jours);
            }

            // Générer la signature numérique
            if (empty($ordonnance->signature_numerique)) {
                $ordonnance->signature_numerique = self::generateSignature($ordonnance);
            }
        });

        static::created(function ($ordonnance) {
            // Générer le QR Code après création
            $ordonnance->generateQrCode();
        });
    }

    // Relations
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
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

    public function lignes(): HasMany
    {
        return $this->hasMany(OrdonnanceLigne::class);
    }

    public function dispensations(): HasMany
    {
        return $this->hasMany(Dispensation::class);
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(CommandePharmacie::class);
    }

    // Scopes
    public function scopeValides($query)
    {
        return $query->where('statut', 'active')
                    ->where(function($q) {
                        $q->whereNull('date_expiration')
                          ->orWhere('date_expiration', '>=', now());
                    });
    }

    public function scopeExpirees($query)
    {
        return $query->where('date_expiration', '<', now())
                    ->orWhere('statut', 'expiree');
    }

    public function scopeDispensees($query)
    {
        return $query->where('statut', 'dispensee');
    }

    public function scopePatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopePraticien($query, $praticienId)
    {
        return $query->where('praticien_id', $praticienId);
    }

    // Méthodes métier
    public static function generateNumeroOrdonnance(): string
    {
        $prefix = 'ORD';
        $year = date('Y');
        $random = strtoupper(Str::random(8));

        return "{$prefix}-{$year}-{$random}";
    }

    public static function generateSignature($ordonnance): string
    {
        $data = [
            'patient_id' => $ordonnance->patient_id,
            'praticien_id' => $ordonnance->praticien_id,
            'date' => $ordonnance->date_ordonnance,
            'diagnostic' => $ordonnance->diagnostic,
        ];

        return hash('sha256', json_encode($data) . config('app.key'));
    }

    public function generateQrCode(): void
    {
        $url = route('ordonnances.verify', ['numero' => $this->numero_ordonnance]);

        $qrCode = base64_encode(
            QrCode::format('png')
                ->size(200)
                ->errorCorrection('H')
                ->generate($url)
        );

        $this->qr_code = 'data:image/png;base64,' . $qrCode;
        $this->saveQuietly();
    }

    public function estValide(): bool
    {
        if ($this->statut !== 'active') {
            return false;
        }

        if ($this->date_expiration && $this->date_expiration < now()) {
            return false;
        }

        return true;
    }

    public function peutEtreRenouvelee(): bool
    {
        if (!$this->renouvelable) {
            return false;
        }

        if ($this->nombre_renouvellements > 0 &&
            $this->renouvellements_effectues >= $this->nombre_renouvellements) {
            return false;
        }

        return $this->estValide();
    }

    public function renouveler(): ?Ordonnance
    {
        if (!$this->peutEtreRenouvelee()) {
            return null;
        }

        // Créer une nouvelle ordonnance basée sur l'ancienne
        $nouvelleOrdonnance = $this->replicate();
        $nouvelleOrdonnance->numero_ordonnance = self::generateNumeroOrdonnance();
        $nouvelleOrdonnance->date_ordonnance = now();
        $nouvelleOrdonnance->renouvellements_effectues = 0;

        // Ajouter une référence à l'ordonnance originale
        $metadata = $nouvelleOrdonnance->metadata ?? [];
        $metadata['ordonnance_origine'] = $this->numero_ordonnance;
        $metadata['numero_renouvellement'] = $this->renouvellements_effectues + 1;
        $nouvelleOrdonnance->metadata = $metadata;

        $nouvelleOrdonnance->save();

        // Copier les lignes de l'ordonnance
        foreach ($this->lignes as $ligne) {
            $nouvelleLigne = $ligne->replicate();
            $nouvelleLigne->ordonnance_id = $nouvelleOrdonnance->id;
            $nouvelleLigne->save();
        }

        // Incrémenter le compteur de renouvellements
        $this->increment('renouvellements_effectues');

        return $nouvelleOrdonnance;
    }

    public function marquerDispensee(int $pharmacieId = null): void
    {
        $this->statut = 'dispensee';

        $metadata = $this->metadata ?? [];
        $metadata['date_dispensation'] = now()->toDateTimeString();

        if ($pharmacieId) {
            $metadata['pharmacie_id'] = $pharmacieId;
        }

        $this->metadata = $metadata;
        $this->save();
    }

    public function marquerPartiellemmentDispensee(array $lignesDispensees): void
    {
        $this->statut = 'partiellement_dispensee';

        $metadata = $this->metadata ?? [];
        $metadata['lignes_dispensees'] = $lignesDispensees;
        $metadata['date_dispensation_partielle'] = now()->toDateTimeString();

        $this->metadata = $metadata;
        $this->save();
    }

    public function annuler(string $motif): void
    {
        $this->statut = 'annulee';

        $metadata = $this->metadata ?? [];
        $metadata['motif_annulation'] = $motif;
        $metadata['date_annulation'] = now()->toDateTimeString();

        $this->metadata = $metadata;
        $this->save();
    }

    // Méthodes de vérification
    public function verifierInteractionsMedicamenteuses(): array
    {
        $interactions = [];
        $medicaments = $this->lignes->pluck('produit');

        // Ici, implémenter la logique de vérification des interactions
        // Pour l'instant, retourner un tableau vide

        return $interactions;
    }

    public function verifierContrIndications(): array
    {
        $contrIndications = [];

        if (!$this->patient) {
            return $contrIndications;
        }

        // Vérifier les allergies du patient
        $dme = $this->patient->dossierMedical;
        if ($dme && $dme->allergies) {
            foreach ($this->lignes as $ligne) {
                if ($ligne->produit) {
                    foreach ($dme->allergies as $allergie) {
                        if (stripos($ligne->produit->dci, $allergie) !== false) {
                            $contrIndications[] = [
                                'type' => 'allergie',
                                'produit' => $ligne->produit->nom_commercial,
                                'detail' => "Allergie connue : {$allergie}"
                            ];
                        }
                    }
                }
            }
        }

        return $contrIndications;
    }

    // Méthodes de calcul
    public function getMontantTotalAttribute(): float
    {
        return $this->lignes->sum(function ($ligne) {
            return $ligne->quantite * ($ligne->produit->prix_unitaire ?? 0);
        });
    }

    public function getNombreMedicamentsAttribute(): int
    {
        return $this->lignes->count();
    }

    // Méthodes de formatage
    public function getStatutBadgeAttribute(): string
    {
        $badges = [
            'active' => '<span class="badge bg-success">Active</span>',
            'dispensee' => '<span class="badge bg-info">Dispensée</span>',
            'partiellement_dispensee' => '<span class="badge bg-warning">Partiellement dispensée</span>',
            'expiree' => '<span class="badge bg-secondary">Expirée</span>',
            'annulee' => '<span class="badge bg-danger">Annulée</span>',
        ];

        return $badges[$this->statut] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getTypeOrdonnanceBadgeAttribute(): string
    {
        $badges = [
            'normale' => '<span class="badge bg-primary">Normale</span>',
            'secure' => '<span class="badge bg-danger">Sécurisée</span>',
            'exception' => '<span class="badge bg-warning">Exception</span>',
            'hospitaliere' => '<span class="badge bg-info">Hospitalière</span>',
        ];

        return $badges[$this->type_ordonnance] ?? '<span class="badge bg-secondary">Standard</span>';
    }
}
