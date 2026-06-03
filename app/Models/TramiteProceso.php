<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TramiteProceso extends Model
{
    protected $table = 'tramite_procesos';

    protected $fillable = [
        'tramite_id', 'area', 'numero_notificacion', 'observacion', 'estado', 'orden', 'archivo_pdf',
    ];

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    public function getBadgeColorAttribute(): string
    {
        return match($this->estado) {
            'finalizado' => 'success',
            'en_proceso' => 'warning',
            default      => 'gray',
        };
    }

    public function getBadgeLabelAttribute(): string
    {
        return match($this->estado) {
            'finalizado' => 'Finalizado',
            'en_proceso' => 'En proceso',
            default      => 'Pendiente',
        };
    }
}
