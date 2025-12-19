<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StructureOpeningHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'structure_id',
        'day_of_week',
        'open_time',
        'close_time',
        'ferme',
    ];

    protected $casts = [
        'ferme' => 'boolean',
    ];

    public function structure()
    {
        return $this->belongsTo(MedicalStructure::class, 'structure_id');
    }
}
