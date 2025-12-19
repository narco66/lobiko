<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProduitPharmaceutique extends Model
{
    use HasFactory;

    protected $table = 'produits_pharmaceutiques';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}
