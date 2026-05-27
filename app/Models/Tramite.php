<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tramite extends Model
{
    protected $table = 'tramites';

    protected $fillable = [
        'numero', 'tipo', 'entidad_nombre', 'entidad_expediente', 'codigo_tramite',
        'area_actual', 'encargado', 'doc_presentado', 'encargado_area',
        'contacto', 'apunte_adicional', 'fecha_ingreso', 'fecha_modificacion',
        'fecha_vencimiento', 'estado', 'activo',
    ];

    protected $casts = [
        'fecha_ingreso'      => 'date',
        'fecha_modificacion' => 'date',
        'fecha_vencimiento'  => 'date',
        'activo'             => 'boolean',
    ];

    public function procesos(): HasMany
    {
        return $this->hasMany(TramiteProceso::class)->orderBy('orden');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getBadgeColorAttribute(): string
    {
        return match($this->estado) {
            'en_tramite'    => 'warning',
            'observado'     => 'danger',
            'firma_final'   => 'info',
            'mesa_de_partes'=> 'primary',
            'aprobado'      => 'success',
            'rechazado'     => 'gray',
            default         => 'gray',
        };
    }

    public function getBadgeLabelAttribute(): string
    {
        return match($this->estado) {
            'en_tramite'    => 'En trámite',
            'observado'     => 'Observado',
            'firma_final'   => 'Firma final',
            'mesa_de_partes'=> 'Mesa de Partes',
            'aprobado'      => 'Aprobado',
            'rechazado'     => 'Rechazado',
            default         => ucfirst($this->estado),
        };
    }
}
