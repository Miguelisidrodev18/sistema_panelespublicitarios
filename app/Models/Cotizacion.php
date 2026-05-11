<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'empresa_id', 'numero', 'cliente_nombre', 'cliente_empresa',
        'cliente_telefono', 'cliente_email', 'tipo_contrato', 'monto_propuesto',
        'fecha_cotizacion', 'fecha_vencimiento', 'notas', 'estado',
    ];

    protected $casts = [
        'monto_propuesto'  => 'decimal:2',
        'fecha_cotizacion' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function elementos(): HasMany
    {
        return $this->hasMany(CotizacionElemento::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}
