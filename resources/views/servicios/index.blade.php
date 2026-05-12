@extends('layouts.app')

@section('title', 'Servicios')
@section('subtitle', 'Gestión de servicios disponibles')

@section('content')
<div class="page-header">
    <div></div>
    <a href="{{ route('servicios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i>Nuevo Servicio
    </a>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Monto base</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($servicios as $srv)
                <tr>
                    <td class="fw-700"><i class="bi bi-{{ $srv->icono ?? 'box' }}" style="margin-right:6px;color:var(--primary)"></i>{{ $srv->nombre }}</td>
                    <td class="text-muted">{{ Str::limit($srv->descripcion, 60) ?? '—' }}</td>
                    <td class="fw-700" style="color:#059669">S/. {{ number_format($srv->monto, 2, '.', ',') }}</td>
                    <td>
                        @if($srv->activo)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-gray">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('servicios.edit', $srv) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('servicios.destroy', $srv) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar este servicio?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No hay servicios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($servicios->hasPages())
    <div class="card-footer">{{ $servicios->links() }}</div>
    @endif
</div>
@endsection
