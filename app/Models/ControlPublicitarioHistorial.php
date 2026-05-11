<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlPublicitarioHistorial extends Model
{
    protected $table = 'control_publicitario_historial';

    protected $fillable = [
        'control_publicitario_id', 'estado_anterior', 'estado_nuevo', 'notas', 'usuario_id',
    ];

    public function control(): BelongsTo
    {
        return $this->belongsTo(ControlPublicitario::class, 'control_publicitario_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
