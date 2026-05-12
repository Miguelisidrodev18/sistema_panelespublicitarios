@extends('layouts.app')

@section('title', 'Paneles Digitales')
@section('subtitle', 'Gestión de paneles digitales')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">{{ $paneles->total() }} panel(es)</span>
    </div>
    @if(auth()->user()->esAdmin())
    <a href="{{ route('paneles-digitales.create') }}" class="btn btn-primary">
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
            <div style="height:140px;background:linear-gradient(135deg,#1E293B 0%,#334155 100%);display:flex;align-items:center;justify-content:center">
                <i class="bi bi-display" style="font-size:40px;color:rgba(255,255,255,.25)"></i>
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
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap">
                @if($panel->codigo)<code>{{ $panel->codigo }}</code>@endif
                @if($panel->direccion)<span style="font-size:12px;color:var(--text-light)"><i class="bi bi-geo-alt" style="color:var(--primary)"></i>{{ Str::limit($panel->direccion, 35) }}</span>@endif
            </div>
            <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:8px">
                @if($panel->medidas)<span class="badge badge-gray">{{ $panel->medidas }}</span>@endif
                @if($panel->tandas)<span class="badge badge-gray">{{ $panel->tandas }} tandas</span>@endif
            </div>
            <div style="font-size:12px;color:var(--text-light);margin-top:6px">
                <i class="bi bi-building" style="margin-right:4px"></i>{{ $panel->empresas->count() }} empresa(s)
            </div>
        </div>
        @if(auth()->user()->esAdmin())
        <div class="wh-footer">
            <a href="{{ route('paneles-digitales.edit', $panel) }}" class="btn btn-sm btn-warning" style="flex:1"><i class="bi bi-pencil"></i>Editar</a>
            <form action="{{ route('paneles-digitales.destroy', $panel) }}" method="POST" onsubmit="return confirm('¿Desactivar este panel?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger btn-icon"><i class="bi bi-x-lg"></i></button>
            </form>
        </div>
        @endif
    </div>
    @empty
    <div style="grid-column:1/-1">
        <div class="card"><div class="empty-state"><i class="bi bi-display"></i><p>No hay paneles digitales registrados</p></div></div>
    </div>
    @endforelse
</div>

@if($paneles->hasPages())
<div style="margin-top:16px">{{ $paneles->links() }}</div>
@endif
@endsection
