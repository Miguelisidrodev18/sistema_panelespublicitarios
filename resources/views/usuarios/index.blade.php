@extends('layouts.app')

@section('title', 'Usuarios')
@section('subtitle', 'Gestión de accesos y permisos')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">
            {{ $usuarios->count() }} usuario(s)
        </span>
    </div>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i>Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre completo</th>
                    <th>Rol</th>
                    <th>Empresa</th>
                    <th>Último acceso</th>
                    <th>Estado</th>
                    <th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td>
                        <div class="flex flex-center gap-8">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;font-weight:700;color:#fff">
                                {{ strtoupper(substr($usuario->username,0,1)) }}
                            </div>
                            <span class="fw-600">{{ $usuario->username }}</span>
                        </div>
                    </td>
                    <td>{{ $usuario->nombre_completo }}</td>
                    <td>
                        @if($usuario->rol === 'admin')
                            <span class="badge badge-danger"><i class="bi bi-shield-fill"></i>Admin</span>
                        @else
                            <span class="badge badge-info"><i class="bi bi-building"></i>Empresa</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $usuario->empresa->nombre ?? '—' }}</td>
                    <td class="text-muted fs-13">{{ $usuario->ultimo_acceso?->diffForHumans() ?? 'Nunca' }}</td>
                    <td>
                        @if($usuario->activo)
                            <span class="badge badge-success"><i class="bi bi-circle-fill" style="font-size:7px"></i>Activo</span>
                        @else
                            <span class="badge badge-gray">Inactivo</span>
                        @endif
                    </td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($usuario->id !== auth()->id())
                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                onsubmit="return confirm('¿Desactivar este usuario?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon" title="Desactivar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <p>No hay usuarios registrados</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
