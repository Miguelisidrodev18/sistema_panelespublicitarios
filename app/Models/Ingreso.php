<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    protected $fillable = [
        'empresa_id', 'tipo', 'monto', 'metodo_pago', 'concepto',
        'observaciones', 'comprobante', 'va_a_general', 'creado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'va_a_general' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
