<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'password', 'email', 'nombre_completo',
        'rol', 'empresa_id', 'activo', 'permisos', 'ultimo_acceso',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'activo' => 'boolean',
        'permisos' => 'array',
        'ultimo_acceso' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esGerencia(): bool
    {
        return $this->rol === 'gerencia';
    }

    public function esEmpresa(): bool
    {
        return $this->rol === 'empresa';
    }

    public function tienePermiso(string $permiso): bool
    {
        if ($this->esAdmin() || $this->esGerencia()) {
            return true;
        }

        $permisos = $this->permisos ?? [];
        return in_array($permiso, $permisos);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
