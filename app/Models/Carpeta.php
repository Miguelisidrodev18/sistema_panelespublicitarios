<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Carpeta extends Model
{
    protected $fillable = ['nombre', 'carpeta_padre_id'];

    public function padre(): BelongsTo
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_padre_id');
    }

    public function subcarpetas(): HasMany
    {
        return $this->hasMany(Carpeta::class, 'carpeta_padre_id');
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(Archivo::class);
    }
}
