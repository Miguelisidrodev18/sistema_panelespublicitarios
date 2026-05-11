<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Egreso extends Model
{
    protected $fillable = [
        'empresa_id', 'tipo', 'monto', 'concepto',
        'observaciones', 'comprobante', 'creado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
