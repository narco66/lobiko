<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProduitPharmaceutique extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'produits_pharmaceutiques';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'generique' => 'boolean',
        'prescription_obligatoire' => 'boolean',
        'stupefiant' => 'boolean',
        'liste_i' => 'boolean',
        'liste_ii' => 'boolean',
        'remboursable' => 'boolean',
        'disponible' => 'boolean',
        'rupture_stock' => 'boolean',
        'prix_unitaire' => 'decimal:2',
        'prix_boite' => 'decimal:2',
        'taux_remboursement' => 'decimal:2',
        'stock_minimum' => 'integer',
        'stock_alerte' => 'integer',
        'temperature_min' => 'integer',
        'temperature_max' => 'integer',
    ];
}
