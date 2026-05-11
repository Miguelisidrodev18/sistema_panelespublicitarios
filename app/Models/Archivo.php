<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archivo extends Model
{
    protected $fillable = [
        'carpeta_id', 'nombre_original', 'nombre_archivo', 'tipo_archivo', 'tamano',
    ];

    public function carpeta(): BelongsTo
    {
        return $this->belongsTo(Carpeta::class);
    }
}
