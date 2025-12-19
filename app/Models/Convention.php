<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Convention extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function assureur()
    {
        return $this->belongsTo(Partner::class, 'assureur_partner_id');
    }

    public function prestataire()
    {
        return $this->belongsTo(Partner::class, 'prestataire_partner_id');
    }

    public function rules()
    {
        return $this->hasMany(ConventionRule::class);
    }
}
