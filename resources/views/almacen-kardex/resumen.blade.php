@extends('layouts.app')

@section('title', 'Stock Actual')
@section('subtitle', 'Almacén de herramientas, material y equipo de personal')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Almacén de herramientas, material y equipo de personal</div>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('almacen-kardex.resumen.imprimir', request()->query()) }}" target="_blank" class="btn btn-secondary"><i class="bi bi-printer"></i>Imprimir</a>
        <a href="{{ route('almacen-kardex.resumen.pdf', request()->query()) }}" class="btn btn-danger"><i class="bi bi-file-pdf"></i>PDF</a>
    </div>
</div>

{{-- Filtros --}}
<form class="filter-bar" method="GET" action="{{ route('almacen-kardex.resumen') }}">
    <select name="responsable_id" class="form-select" style="max-width:250px">
        <option value="">Todos los responsables</option>
        @foreach($usuarios as $u)
            <option value="{{ $u->id }}" {{ request('responsable_id') == $u->id ? 'selected' : '' }}>{{ $u->nombre_completo }}</option>
        @endforeach
    </select>
    <select name="tipo" class="form-select" style="max-width:200px">
        <option value="">Todos los tipos</option>
        @foreach(\App\Models\AlmacenItem::TIPOS as $key => $label)
            <option value="{{ $key }}" {{ request('tipo') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i>Filtrar</button>
</form>

@forelse($itemsPorTipo as $tipo => $itemsGrupo)
<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span>
            <i class="bi bi-{{ $tipo === 'materiales' ? 'bricks' : ($tipo === 'maquina' ? 'gear' : ($tipo === 'herramienta' ? 'wrench' : 'person-badge')) }}"></i>
            {{ \App\Models\AlmacenItem::TIPOS[$tipo] ?? ucfirst($tipo) }}
        </span>
        <span class="badge badge-gray">{{ $itemsGrupo->count() }} ítems</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Almacén</th>
                    <th class="text-center">Cantidad</th>
                    <th>Unidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itemsGrupo as $item)
                <tr>
                    <td class="fw-600">
                        <a href="{{ route('almacen-kardex.index', ['almacen_item_id' => $item->id]) }}" style="color:inherit;text-decoration:none">
                            {{ $item->nombre }}
                        </a>
                    </td>
                    <td><code>{{ $item->codigo ?? '—' }}</code></td>
                    <td>{{ $item->almacen->nombre ?? '—' }}</td>
                    <td class="text-center fw-700" style="font-size:15px">
                        {{ number_format($item->stock_actual, $item->tipo === 'materiales' ? 2 : 0) }}
                    </td>
                    <td>{{ $item->unidad_medida }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="card">
    <div class="empty-state" style="padding:40px">
        <i class="bi bi-clipboard-data" style="font-size:2rem;opacity:.4"></i>
        <p>No hay ítems con stock disponible</p>
    </div>
</div>
@endforelse
@endsection
