<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Empresa extends Model
{
    protected $fillable = [
        'nombre', 'correo', 'celular', 'panel_digital', 'panel_tradicional', 'marketing_digital',
        'otros_servicios', 'tipo_contrato', 'detalles_convenio', 'bonificacion',
        'adendas_pagos', 'comentario_bonificacion', 'comentario_adendas',
        'encargado', 'monto', 'dias_duracion', 'fecha_inicio', 'fecha_fin',
        'contrato_pdf', 'activo',
    ];

    protected $casts = [
        'panel_digital' => 'boolean',
        'panel_tradicional' => 'boolean',
        'marketing_digital' => 'boolean',
        'activo' => 'boolean',
        'bonificacion' => 'boolean',
        'adendas_pagos' => 'boolean',
        'monto' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }

    public function cobranzas(): HasMany
    {
        return $this->hasMany(Cobranza::class);
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class);
    }

    public function egresos(): HasMany
    {
        return $this->hasMany(Egreso::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoEmpresa::class);
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'empresa_servicios')->withPivot('monto');
    }

    public function panalesDigitales(): BelongsToMany
    {
        return $this->belongsToMany(PanelDigital::class, 'empresa_paneles_digitales', 'empresa_id', 'panel_id');
    }

    public function panalesTradicionales(): BelongsToMany
    {
        return $this->belongsToMany(PanelUbicacion::class, 'empresa_paneles_tradicionales', 'empresa_id', 'panel_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
