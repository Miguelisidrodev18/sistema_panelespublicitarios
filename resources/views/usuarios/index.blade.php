@extends('layouts.app')

@section('title', 'Usuarios')
@section('subtitle', 'Gestión de cuentas de acceso')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">{{ $usuarios->count() }} usuario(s) registrado(s)</span>
    </div>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i>Nuevo Usuario</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th><th>Nombre completo</th><th>Rol</th><th>Empresa</th><th>Estado</th><th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $user)
                <tr>
                    <td>
                        <div class="flex flex-center gap-12">
                            <div class="user-avatar">{{ strtoupper(substr($user->username, 0, 2)) }}</div>
                            <div class="fw-700" style="color:var(--text-dark)">{{ $user->username }}</div>
                        </div>
                    </td>
                    <td>{{ $user->nombre_completo }}</td>
                    <td>
                        @if($user->rol === 'admin')
                            <span class="badge badge-primary"><i class="bi bi-shield-check" style="font-size:10px"></i>Admin</span>
                        @else
                            <span class="badge badge-info">Empresa</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $user->empresa->nombre ?? '—' }}</td>
                    <td>
                        @if($user->activo)
                            <span class="badge badge-success"><i class="bi bi-circle-fill dot"></i>Activo</span>
                        @else
                            <span class="badge badge-gray">Inactivo</span>
                        @endif
                    </td>
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                            <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-sm btn-warning btn-icon" title="Editar"><i class="bi bi-pencil"></i></a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('usuarios.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Eliminar este usuario?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-person"></i><p>No hay usuarios registrados</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
