<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Servicio extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'icono', 'monto', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
        'monto' => 'decimal:2',
    ];

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_servicios')->withPivot('monto');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
