<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    protected $fillable = [
        'cotizacion_id', 'numero_contrato', 'empresa_id', 'contratante', 'doc_tipo', 'doc_numero',
        'direccion', 'tipo_contrato', 'monto_total', 'adelanto', 'saldo_pendiente',
        'fecha_inicio', 'fecha_fin', 'descripcion', 'estado', 'frecuencia_cobro',
    ];

    protected $casts = [
        'monto_total'      => 'decimal:2',
        'adelanto'         => 'decimal:2',
        'saldo_pendiente'  => 'decimal:2',
        'fecha_inicio'     => 'date',
        'fecha_fin'        => 'date',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cotizacion::class);
    }

    public function elementos(): HasMany
    {
        return $this->hasMany(ContratoElemento::class);
    }

    public function cobros(): HasMany
    {
        return $this->hasMany(ContratoCobro::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function mesesFrecuencia(): int
    {
        return match ($this->frecuencia_cobro) {
            'bimestral'  => 2,
            'trimestral' => 3,
            'semestral'  => 6,
            'anual'      => 12,
            default      => 1,
        };
    }

    public function getEstadoDeudaAttribute(): string
    {
        if ($this->estado === 'cancelado') {
            return 'Cancelado';
        }
        if ((float) $this->saldo_pendiente <= 0) {
            return 'Al día';
        }
        // Hay saldo pendiente: determinar si ya debería haberse cobrado algo
        $ultimoCobro = $this->cobros->sortByDesc('fecha_cobro')->first();
        if ($ultimoCobro) {
            $proximaFecha = $ultimoCobro->fecha_cobro->copy()->addMonths($this->mesesFrecuencia());
            if ($proximaFecha->isPast()) {
                return 'Moroso';
            }
            return 'Pendiente';
        }
        // Sin cobros registrados: si ya pasó la fecha de inicio, es moroso
        if ($this->fecha_inicio && $this->fecha_inicio->isPast()) {
            return 'Moroso';
        }
        return 'Pendiente';
    }
}
