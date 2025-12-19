<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlerteStock extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'alertes_stock';

    protected $fillable = [
        'pharmacie_id',
        'stock_medicament_id',
        'type_alerte',
        'message',
        'vue',
        'traitee',
        'date_traitement',
        'traite_par',
        'action_prise',
    ];

    protected $casts = [
        'vue' => 'boolean',
        'traitee' => 'boolean',
        'date_traitement' => 'datetime',
    ];

    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class, 'pharmacie_id');
    }

    public function stockMedicament(): BelongsTo
    {
        return $this->belongsTo(StockMedicament::class, 'stock_medicament_id');
    }
}
