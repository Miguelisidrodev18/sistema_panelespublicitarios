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

<div class="card">
    <div class="card-header ch-green">
        <span><i class="bi bi-box-seam"></i>Servicios disponibles</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Monto base</th>
                    <th>Estado</th>
                    <th class="td-end">Acciones</th>
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
                    <td class="td-end">
                        <div class="flex flex-center gap-8" style="justify-content:flex-end">
                        <a href="{{ route('servicios.edit', $srv) }}" class="btn btn-sm btn-warning btn-icon"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('servicios.destroy', $srv) }}" method="POST" onsubmit="return confirm('¿Desactivar este servicio?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-x-lg"></i></button>
                        </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5"><div class="empty-state"><i class="bi bi-box-seam"></i><p>No hay servicios registrados.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($servicios->hasPages())
    <div class="card-footer">{{ $servicios->links() }}</div>
    @endif
</div>
@endsection
