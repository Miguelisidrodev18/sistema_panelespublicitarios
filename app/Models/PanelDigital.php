<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PanelDigital extends Model
{
    protected $table = 'paneles_digitales';

    protected $fillable = [
        'codigo', 'nombre', 'direccion', 'medidas', 'resolucion',
        'orientacion', 'tandas', 'costo_produccion', 'foto', 'lat', 'lng', 'activo',
    ];

    protected $casts = [
        'activo'           => 'boolean',
        'lat'              => 'decimal:8',
        'lng'              => 'decimal:8',
        'costo_produccion' => 'decimal:2',
    ];

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_paneles_digitales', 'panel_id', 'empresa_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
