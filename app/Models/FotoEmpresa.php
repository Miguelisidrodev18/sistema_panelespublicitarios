<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoEmpresa extends Model
{
    protected $table = 'fotos_empresas';

    protected $fillable = [
        'empresa_id', 'nombre_archivo', 'ruta_archivo', 'descripcion', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
