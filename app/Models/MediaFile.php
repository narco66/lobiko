<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MediaFile extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'path',
        'disk',
        'mime',
        'size',
        'original_name',
        'alt_text',
        'caption',
        'uploader_id',
        'mediable_type',
        'mediable_id',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function mediable()
    {
        return $this->morphTo();
    }
}
