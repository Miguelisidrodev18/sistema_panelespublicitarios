@extends('layouts.app')

@section('title', 'Almacenes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="{{ route('almacenes.create') }}" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Almacén
    </a>
</div>

<div class="row g-3">
    @forelse($almacenes as $almacen)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 {{ $almacen->es_principal ? 'border-danger border-2' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-semibold mb-0">{{ $almacen->nombre }}</h6>
                        @if($almacen->codigo)
                            <div class="small text-muted"><code>{{ $almacen->codigo }}</code></div>
                        @endif
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        @if($almacen->es_principal)
                            <span class="badge bg-danger">Principal</span>
                        @endif
                        <span class="badge bg-{{ $almacen->estado === 'activo' ? 'success' : 'secondary' }}">
                            {{ ucfirst($almacen->estado) }}
                        </span>
                    </div>
                </div>

                @if($almacen->direccion)
                <div class="small text-muted mb-1">
                    <i class="bi bi-geo-alt me-1"></i>{{ $almacen->direccion }}
                </div>
                @endif
                @if($almacen->telefono)
                <div class="small text-muted mb-1">
                    <i class="bi bi-telephone me-1"></i>{{ $almacen->telefono }}
                </div>
                @endif
                @if($almacen->responsable)
                <div class="small text-muted">
                    <i class="bi bi-person me-1"></i>{{ $almacen->responsable }}
                </div>
                @endif
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a href="{{ route('almacenes.edit', $almacen) }}" class="btn btn-sm btn-outline-warning flex-fill">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                @if($almacen->estado === 'activo')
                <form action="{{ route('almacenes.destroy', $almacen) }}" method="POST"
                    onsubmit="return confirm('¿Desactivar este almacén?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-building fs-1 d-block mb-3"></i>No hay almacenes registrados.
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
