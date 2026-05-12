<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'usuario_id', 'accion', 'modulo', 'modelo_id',
        'descripcion', 'datos_anteriores', 'datos_nuevos', 'ip',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public static function registrar(string $accion, string $modulo, ?int $modeloId = null, ?string $descripcion = null, ?array $anterior = null, ?array $nuevo = null): void
    {
        static::create([
            'usuario_id' => auth()->id(),
            'accion' => $accion,
            'modulo' => $modulo,
            'modelo_id' => $modeloId,
            'descripcion' => $descripcion,
            'datos_anteriores' => $anterior,
            'datos_nuevos' => $nuevo,
            'ip' => request()->ip(),
        ]);
    }
}
