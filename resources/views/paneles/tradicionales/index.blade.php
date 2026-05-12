@extends('layouts.app')

@section('title', 'Paneles Tradicionales')
@section('subtitle', 'Gestión de paneles tradicionales')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">{{ $paneles->total() }} panel(es)</span>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('paneles-tradicionales.create') }}" class="btn btn-warning">
        <i class="bi bi-plus-lg"></i>Nuevo Panel
    </a>
    @endif
</div>

<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
    @forelse($paneles as $panel)
    <div class="warehouse-card hover-lift">
        @if($panel->foto)
            <img src="{{ Storage::url($panel->foto) }}" style="width:100%;height:160px;object-fit:cover;display:block" alt="{{ $panel->nombre }}">
        @else
            <div style="height:140px;background:linear-gradient(135deg,#92400E 0%,#F59E0B 100%);display:flex;align-items:center;justify-content:center">
                <i class="bi bi-signpost-2" style="font-size:40px;color:rgba(255,255,255,.25)"></i>
            </div>
        @endif
        <div class="wh-body">
            <div class="flex flex-between" style="align-items:flex-start;margin-bottom:6px">
                <div class="fw-700" style="font-size:14px;color:var(--text-dark)">{{ $panel->nombre }}</div>
                @if($panel->activo)
                    <span class="badge badge-success"><i class="bi bi-circle-fill dot"></i>Activo</span>
                @else
                    <span class="badge badge-gray">Inactivo</span>
                @endif
            </div>
            @if($panel->codigo)<div style="margin-bottom:4px"><code>{{ $panel->codigo }}</code></div>@endif
            @if($panel->direccion)
            <div style="font-size:12px;color:var(--text-light);margin-bottom:2px"><i class="bi bi-geo-alt" style="margin-right:4px;color:#F59E0B"></i>{{ Str::limit($panel->direccion, 40) }}</div>
            @endif
            <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:8px">
                @if($panel->caras)<span class="badge badge-warning"><i class="bi bi-layout-split" style="font-size:10px"></i>{{ $panel->caras }} cara(s)</span>@endif
            </div>
            <div style="font-size:12px;color:var(--text-light);margin-top:6px">
                <i class="bi bi-building" style="margin-right:4px"></i>{{ $panel->empresas->count() }} empresa(s)
            </div>
        </div>
        @if(auth()->user()->esAdmin())
        <div class="wh-footer">
            <a href="{{ route('paneles-tradicionales.edit', $panel) }}" class="btn btn-sm btn-warning" style="flex:1"><i class="bi bi-pencil"></i>Editar</a>
            <form action="{{ route('paneles-tradicionales.destroy', $panel) }}" method="POST" onsubmit="return confirm('¿Desactivar este panel?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-x-lg"></i></button>
            </form>
        </div>
        @endif
    </div>
    @empty
    <div style="grid-column:1/-1">
        <div class="card"><div class="empty-state"><i class="bi bi-signpost-2"></i><p>No hay paneles tradicionales registrados</p></div></div>
    </div>
    @endforelse
</div>

@if($paneles->hasPages())
<div style="margin-top:16px">{{ $paneles->links() }}</div>
@endif
@endsection
