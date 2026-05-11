<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoElemento extends Model
{
    protected $table = 'contrato_elementos';

    protected $fillable = [
        'contrato_id', 'tipo_elemento', 'panel_id', 'codigo', 'tiempo_contrato',
        'observaciones', 'estado_instalacion', 'fecha_instalacion', 'fecha_retiro',
    ];

    protected $casts = [
        'fecha_instalacion' => 'date',
        'fecha_retiro'      => 'date',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }
}
