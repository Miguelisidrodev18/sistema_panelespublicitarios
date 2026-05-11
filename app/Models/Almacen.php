<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacenes';

    protected $fillable = [
        'nombre', 'codigo', 'direccion', 'telefono', 'responsable', 'estado', 'es_principal',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
