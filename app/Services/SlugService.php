<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SlugService
{
    public function generate(Model $model, string $title, string $column = 'slug'): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while ($model->newQuery()->where($column, $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
