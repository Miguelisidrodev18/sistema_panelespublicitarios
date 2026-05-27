<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CotizacionElemento extends Model
{
    protected $table = 'cotizacion_elementos';

    protected $fillable = [
        'cotizacion_id', 'tipo_elemento', 'subtipo', 'panel_id', 'servicio_id', 'codigo',
        'tiempo_contrato', 'precio_unitario', 'costo_produccion', 'desc_costo', 'observaciones',
    ];

    protected $casts = [
        'precio_unitario'  => 'decimal:2',
        'costo_produccion' => 'decimal:2',
    ];

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}
