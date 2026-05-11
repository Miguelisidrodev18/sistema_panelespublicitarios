<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ControlPublicitario extends Model
{
    protected $table = 'control_publicitario';

    protected $fillable = [
        'empresa_nombre', 'panel_codigo', 'tipo_panel', 'fecha_inicio', 'fecha_fin',
        'estado', 'fecha_cancelacion', 'notas',
    ];

    protected $casts = [
        'fecha_cancelacion' => 'datetime',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function historial(): HasMany
    {
        return $this->hasMany(ControlPublicitarioHistorial::class)->latest();
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
