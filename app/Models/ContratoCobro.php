<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoCobro extends Model
{
    protected $table = 'contratos_cobros';

    protected $fillable = [
        'contrato_id', 'tipo_cobro', 'metodo_pago', 'monto', 'fecha_cobro', 'notas',
    ];

    protected $casts = [
        'monto'      => 'decimal:2',
        'fecha_cobro' => 'date',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }
}
