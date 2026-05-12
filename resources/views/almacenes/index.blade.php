@extends('layouts.app')

@section('title', 'Almacenes')
@section('subtitle', 'Gestión de almacenes')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">{{ $almacenes->count() }} almacén(es)</span>
    </div>
    <a href="{{ route('almacenes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i>Nuevo Almacén</a>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
    @forelse($almacenes as $almacen)
    <div class="warehouse-card {{ $almacen->es_principal ? 'is-primary' : '' }}">
        <div class="wh-body">
            <div class="flex flex-between" style="align-items:flex-start;margin-bottom:10px">
                <div>
                    <div class="fw-700" style="font-size:15px;color:var(--text-dark)">{{ $almacen->nombre }}</div>
                    @if($almacen->codigo)
                        <code style="margin-top:2px;display:inline-block">{{ $almacen->codigo }}</code>
                    @endif
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                    @if($almacen->es_principal)
                        <span class="badge badge-primary"><i class="bi bi-star-fill" style="font-size:9px"></i>Principal</span>
                    @endif
                    <span class="badge badge-{{ $almacen->estado === 'activo' ? 'success' : 'gray' }}">
                        <i class="bi bi-circle-fill dot"></i>{{ ucfirst($almacen->estado) }}
                    </span>
                </div>
            </div>

            @if($almacen->direccion)
            <div style="font-size:12.5px;color:var(--text-light);margin-bottom:4px">
                <i class="bi bi-geo-alt" style="margin-right:4px;color:var(--primary)"></i>{{ $almacen->direccion }}
            </div>
            @endif
            @if($almacen->telefono)
            <div style="font-size:12.5px;color:var(--text-light);margin-bottom:4px">
                <i class="bi bi-telephone" style="margin-right:4px;color:var(--primary)"></i>{{ $almacen->telefono }}
            </div>
            @endif
            @if($almacen->responsable)
            <div style="font-size:12.5px;color:var(--text-light)">
                <i class="bi bi-person" style="margin-right:4px;color:var(--primary)"></i>{{ $almacen->responsable }}
            </div>
            @endif
        </div>
        <div class="wh-footer">
            <a href="{{ route('almacenes.edit', $almacen) }}" class="btn btn-sm btn-warning" style="flex:1">
                <i class="bi bi-pencil"></i>Editar
            </a>
            @if($almacen->estado === 'activo')
            <form action="{{ route('almacenes.destroy', $almacen) }}" method="POST"
                onsubmit="return confirm('¿Desactivar este almacén?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-x-lg"></i></button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1">
        <div class="card">
            <div class="empty-state"><i class="bi bi-building"></i><p>No hay almacenes registrados</p></div>
        </div>
    </div>
    @endforelse
</div>
@endsection
