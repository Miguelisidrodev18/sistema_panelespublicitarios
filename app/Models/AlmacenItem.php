<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlmacenItem extends Model
{
    protected $table = 'almacen_items';

    protected $fillable = [
        'almacen_id', 'nombre', 'codigo', 'marca', 'serie', 'tipo',
        'unidad_medida', 'anio_compra', 'stock_actual', 'proveedor_id', 'estado',
    ];

    protected $casts = [
        'stock_actual' => 'decimal:2',
    ];

    const TIPOS = [
        'maquina'       => 'Máquina',
        'herramienta'   => 'Herramienta',
        'indumentaria'  => 'Indumentaria',
        'materiales'    => 'Materiales',
    ];

    const UNIDADES = [
        'unidad' => 'Unidad(es)',
        'kg'     => 'Kg',
        'saco'   => 'Saco(s)',
        'metro'  => 'Metro(s)',
        'litro'  => 'Litro(s)',
        'rollo'  => 'Rollo(s)',
        'caja'   => 'Caja(s)',
        'pieza'  => 'Pieza(s)',
    ];

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(AlmacenMovimiento::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo);
    }

    public function getUnidadLabelAttribute(): string
    {
        return self::UNIDADES[$this->unidad_medida] ?? $this->unidad_medida;
    }
}
