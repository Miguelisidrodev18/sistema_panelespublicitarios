<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PanelUbicacion extends Model
{
    protected $table = 'paneles_ubicaciones';

    protected $fillable = [
        'codigo', 'nombre', 'direccion', 'caras', 'foto', 'lat', 'lng', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class, 'empresa_paneles_tradicionales', 'panel_id', 'empresa_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
