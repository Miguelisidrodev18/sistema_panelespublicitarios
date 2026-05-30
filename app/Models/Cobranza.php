<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cobranza extends Model
{
    protected $fillable = [
        'empresa_id', 'contrato_id', 'numero_cuota', 'monto', 'fecha_vencimiento', 'estado', 'concepto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_vencimiento' => 'date',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', 'pendiente')->where('fecha_vencimiento', '<', now());
    }
}
