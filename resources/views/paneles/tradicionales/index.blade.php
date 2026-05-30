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
            <div style="position:relative">
                <img src="{{ Storage::url($panel->foto) }}" style="width:100%;height:160px;object-fit:cover;display:block" alt="{{ $panel->nombre }}">
                <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(146,64,14,.4));pointer-events:none"></div>
                @if($panel->activo)
                    <span class="badge badge-success" style="position:absolute;top:10px;right:10px"><i class="bi bi-circle-fill dot"></i>Activo</span>
                @else
                    <span class="badge badge-gray" style="position:absolute;top:10px;right:10px">Inactivo</span>
                @endif
            </div>
        @else
            <div style="height:140px;background:linear-gradient(135deg,#92400E 0%,#B45309 50%,#F59E0B 100%);display:flex;align-items:center;justify-content:center;position:relative;border-bottom:2px solid rgba(245,158,11,.3)">
                <i class="bi bi-signpost-2" style="font-size:44px;color:rgba(255,255,255,.25)"></i>
                @if($panel->activo)
                    <span class="badge badge-success" style="position:absolute;top:10px;right:10px"><i class="bi bi-circle-fill dot"></i>Activo</span>
                @else
                    <span class="badge badge-gray" style="position:absolute;top:10px;right:10px">Inactivo</span>
                @endif
            </div>
        @endif
        <div class="wh-body">
            <div class="fw-700" style="font-size:14px;color:var(--text-dark);margin-bottom:6px">{{ $panel->nombre }}</div>
            @if($panel->codigo)<div style="margin-bottom:6px"><code>{{ $panel->codigo }}</code></div>@endif
            @if($panel->direccion)
            <div style="font-size:12.5px;color:var(--text-light);margin-bottom:6px;display:flex;align-items:center;gap:5px">
                <i class="bi bi-geo-alt-fill" style="color:#F59E0B;font-size:12px"></i>{{ Str::limit($panel->direccion, 40) }}
            </div>
            @endif
            <div style="display:flex;gap:5px;flex-wrap:wrap;margin-top:6px">
                @if($panel->caras)<span class="badge badge-warning"><i class="bi bi-layout-split" style="font-size:10px"></i>{{ $panel->caras }} cara(s)</span>@endif
                @if($panel->medidas)<span class="badge badge-info">{{ $panel->medidas }}</span>@endif
                @if($panel->gramaje_lonas)<span class="badge badge-gray">{{ $panel->gramaje_lonas }}</span>@endif
                <span class="badge badge-gray"><i class="bi bi-building"></i>{{ $panel->empresas->count() }}</span>
            </div>
            @if($panel->costo_produccion)
            <div style="margin-top:8px;padding:7px 10px;background:var(--amber-light);border-radius:7px;display:flex;align-items:center;justify-content:space-between;gap:8px">
                <span style="font-size:11px;color:var(--amber-dark);font-weight:600">
                    <i class="bi bi-cash-coin" style="margin-right:4px"></i>
                    {{ $panel->desc_costo ?? 'Costo de producción' }}
                </span>
                <span style="font-size:13px;font-weight:800;color:var(--amber-dark);white-space:nowrap">
                    S/ {{ number_format($panel->costo_produccion, 2, '.', ',') }}
                </span>
            </div>
            @endif
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
