@extends('layouts.app')

@section('title', 'Paneles Tradicionales')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('paneles-tradicionales.create') }}" class="btn btn-warning">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Panel
    </a>
    @endif
</div>

<div class="row g-3">
    @forelse($paneles as $panel)
    <div class="col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            @if($panel->foto)
                <img src="{{ Storage::url($panel->foto) }}" class="card-img-top" style="height:160px;object-fit:cover">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px">
                    <i class="bi bi-sign-stop fs-1 text-secondary"></i>
                </div>
            @endif
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="fw-semibold mb-0">{{ $panel->nombre }}</h6>
                    @if($panel->activo)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </div>
                @if($panel->codigo)<div class="text-muted small"><code>{{ $panel->codigo }}</code></div>@endif
                @if($panel->direccion)
                    <div class="text-muted small mt-1">
                        <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($panel->direccion, 40) }}
                    </div>
                @endif
                @if($panel->caras)
                    <div class="mt-2 small">
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-layout-split me-1"></i>{{ $panel->caras }} cara(s)
                        </span>
                    </div>
                @endif
                <div class="mt-2 small text-muted">
                    <i class="bi bi-building me-1"></i>{{ $panel->empresas->count() }} empresa(s)
                </div>
            </div>
            @if(auth()->user()->esAdmin())
            <div class="card-footer bg-white d-flex gap-2">
                <a href="{{ route('paneles-tradicionales.edit', $panel) }}" class="btn btn-sm btn-outline-warning flex-fill">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <form action="{{ route('paneles-tradicionales.destroy', $panel) }}" method="POST"
                    onsubmit="return confirm('¿Desactivar este panel?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-sign-stop fs-1 d-block mb-3"></i>No hay paneles tradicionales registrados.
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($paneles->hasPages())
<div class="mt-3">{{ $paneles->links() }}</div>
@endif
@endsection
