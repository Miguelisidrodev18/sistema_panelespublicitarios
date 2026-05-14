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
            <div style="position:relative">
                <img src="{{ Storage::url($panel->foto) }}" style="width:100%;height:160px;object-fit:cover;display:block" alt="{{ $panel->nombre }}">
                <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(15,23,42,.5));pointer-events:none"></div>
                @if($panel->activo)
                    <span class="badge badge-success" style="position:absolute;top:10px;right:10px"><i class="bi bi-circle-fill dot"></i>Activo</span>
                @else
                    <span class="badge badge-gray" style="position:absolute;top:10px;right:10px">Inactivo</span>
                @endif
            </div>
        @else
            <div style="height:140px;background:linear-gradient(135deg,#1E293B 0%,#2D3B55 100%);display:flex;align-items:center;justify-content:center;position:relative">
                <i class="bi bi-display" style="font-size:44px;color:rgba(255,255,255,.2)"></i>
                @if($panel->activo)
                    <span class="badge badge-success" style="position:absolute;top:10px;right:10px"><i class="bi bi-circle-fill dot"></i>Activo</span>
                @else
                    <span class="badge badge-gray" style="position:absolute;top:10px;right:10px">Inactivo</span>
                @endif
            </div>
        @endif
        <div class="wh-body">
            <div class="fw-700" style="font-size:14px;color:var(--text-dark);margin-bottom:6px">{{ $panel->nombre }}</div>
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;flex-wrap:wrap">
                @if($panel->codigo)<code>{{ $panel->codigo }}</code>@endif
            </div>
            @if($panel->direccion)
            <div style="font-size:12.5px;color:var(--text-light);margin-bottom:6px;display:flex;align-items:center;gap:5px">
                <i class="bi bi-geo-alt-fill" style="color:var(--primary);font-size:12px"></i>{{ Str::limit($panel->direccion, 40) }}
            </div>
            @endif
            <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:6px">
                @if($panel->medidas)<span class="badge badge-info">{{ $panel->medidas }}</span>@endif
                @if($panel->tandas)<span class="badge badge-purple">{{ $panel->tandas }} tandas</span>@endif
                @php $activas = (int)($campanasPorPanel[$panel->codigo] ?? 0) @endphp
                @if($activas > 0)
                    <span class="badge" style="background:linear-gradient(135deg,#10B981,#059669);color:#fff"
                          title="{{ $activas }} campaña(s) activa(s) en control publicitario">
                        <i class="bi bi-megaphone-fill"></i> {{ $activas }} en uso
                    </span>
                @else
                    <span class="badge badge-success" title="Sin campañas activas — panel disponible">
                        <i class="bi bi-check-circle-fill"></i> Disponible
                    </span>
                @endif
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
