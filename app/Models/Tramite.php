<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tramite extends Model
{
    /**
     * Fuente única de verdad para los estados del trámite.
     * Añadir o editar estados aquí se refleja en el select, badges y filtros automáticamente.
     */
    const ESTADOS = [
        'en_tramite'     => ['label' => 'En trámite',    'color' => 'warning'],
        'observado'      => ['label' => 'Observado',      'color' => 'danger'],
        'firma_final'    => ['label' => 'Firma final',    'color' => 'info'],
        'mesa_de_partes' => ['label' => 'Mesa de Partes', 'color' => 'primary'],
        'aprobado'       => ['label' => 'Aprobado',       'color' => 'success'],
        'rechazado'      => ['label' => 'Rechazado',      'color' => 'gray'],
    ];

    /** Devuelve [value => label] para usar en un <select> */
    public static function estadosParaSelect(): array
    {
        return array_map(fn($e) => $e['label'], self::ESTADOS);
    }

    protected $table = 'tramites';

    protected $fillable = [
        'numero', 'tipo', 'entidad_nombre', 'entidad_expediente', 'codigo_tramite',
        'area_actual', 'encargado', 'doc_presentado', 'encargado_area',
        'contacto', 'apunte_adicional', 'archivo_pdf', 'fecha_ingreso', 'fecha_modificacion',
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
        return self::ESTADOS[$this->estado]['color'] ?? 'gray';
    }

    public function getBadgeLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado]['label'] ?? ucfirst($this->estado);
    }
}
