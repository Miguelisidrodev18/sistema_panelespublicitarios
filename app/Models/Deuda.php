<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deuda extends Model
{
    protected $fillable = [
        'acreedor', 'concepto', 'monto', 'monto_pendiente',
        'fecha_deuda', 'fecha_vencimiento', 'prioridad', 'notas', 'estado',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'monto_pendiente' => 'decimal:2',
        'fecha_deuda' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoDeuda::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}
