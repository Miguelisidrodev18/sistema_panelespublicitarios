<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    protected $fillable = [
        'empresa_id', 'nombre', 'tipo', 'archivo', 'descripcion', 'tamanio', 'subido_por',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
