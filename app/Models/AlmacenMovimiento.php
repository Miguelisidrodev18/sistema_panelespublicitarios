<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlmacenMovimiento extends Model
{
    protected $table = 'almacen_movimientos';

    protected $fillable = [
        'almacen_item_id', 'tipo_movimiento', 'cantidad', 'saldo',
        'fecha', 'detalle', 'responsable_id', 'panel_digital_id',
        'panel_ubicacion_id', 'proveedor_id', 'registrado_por', 'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'saldo'    => 'decimal:2',
        'fecha'    => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(AlmacenItem::class, 'almacen_item_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable_id');
    }

    public function panelDigital(): BelongsTo
    {
        return $this->belongsTo(PanelDigital::class);
    }

    public function panelUbicacion(): BelongsTo
    {
        return $this->belongsTo(PanelUbicacion::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }

    public function getProyectoNombreAttribute(): ?string
    {
        if ($this->panelDigital) {
            return $this->panelDigital->nombre . ' (Digital)';
        }
        if ($this->panelUbicacion) {
            return $this->panelUbicacion->nombre . ' (Tradicional)';
        }
        return null;
    }
}
