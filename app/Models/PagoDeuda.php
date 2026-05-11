<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoDeuda extends Model
{
    protected $table = 'pagos_deudas';

    protected $fillable = [
        'deuda_id', 'monto', 'fecha_pago', 'metodo_pago', 'notas', 'comprobante',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    public function deuda(): BelongsTo
    {
        return $this->belongsTo(Deuda::class);
    }
}
