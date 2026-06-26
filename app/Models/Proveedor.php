<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'razon_social', 'ruc', 'direccion', 'telefono', 'email',
        'contacto', 'rubro', 'observaciones', 'estado',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(AlmacenItem::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(AlmacenMovimiento::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
