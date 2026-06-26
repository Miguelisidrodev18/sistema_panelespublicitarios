@extends('layouts.app')

@section('title', 'Equipos y Materiales')
@section('subtitle', 'Inventario de almacén')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <span style="font-size:13px;color:var(--text-light);font-weight:500">{{ $items->total() }} ítem(s)</span>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('almacen-kardex.index', ['accion' => 'salida']) }}" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i>+ Salidas</a>
        <a href="{{ route('almacen-items.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i>Nuevo Almacén</a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(150px,1fr));margin-bottom:16px">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['maquina'] }}</div>
        <div class="stat-label">Máquinas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['herramienta'] }}</div>
        <div class="stat-label">Herramientas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['indumentaria'] }}</div>
        <div class="stat-label">Indumentaria</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['materiales'] }}</div>
        <div class="stat-label">Materiales</div>
    </div>
</div>

{{-- Filtros --}}
<form class="filter-bar" method="GET" action="{{ route('almacen-items.index') }}">
    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por nombre, código o marca...">
    <select name="tipo" class="form-select" style="max-width:160px">
        <option value="">Todos los tipos</option>
        @foreach(\App\Models\AlmacenItem::TIPOS as $key => $label)
            <option value="{{ $key }}" {{ request('tipo') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <select name="almacen_id" class="form-select" style="max-width:180px">
        <option value="">Todos los almacenes</option>
        @foreach($almacenes as $almacen)
            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>{{ $almacen->nombre }}</option>
        @endforeach
    </select>
    <select name="estado" class="form-select" style="max-width:140px">
        <option value="">Estado</option>
        <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
        <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
    </select>
    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i>Buscar</button>
</form>

<div class="card">
    <div class="card-header"><span><i class="bi bi-box-seam"></i>Inventario</span></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Almacén</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th class="td-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td class="fw-600">{{ $item->nombre }}</td>
                    <td><code>{{ $item->codigo ?? '—' }}</code></td>
                    <td>
                        @php
                            $badgeColors = ['maquina' => 'info', 'herramienta' => 'warning', 'indumentaria' => 'purple', 'materiales' => 'teal'];
                        @endphp
                        <span class="badge badge-{{ $badgeColors[$item->tipo] ?? 'gray' }}">{{ $item->tipo_label }}</span>
                    </td>
                    <td>{{ $item->marca ?? '—' }}</td>
                    <td>{{ $item->almacen->nombre ?? '—' }}</td>
                    <td class="fw-600">
                        {{ number_format($item->stock_actual, $item->tipo === 'materiales' ? 2 : 0) }}
                        <small style="color:var(--text-light)">{{ $item->unidad_medida }}</small>
                    </td>
                    <td>
                        <span class="badge badge-{{ $item->estado === 'activo' ? 'success' : 'gray' }}">
                            <i class="bi bi-circle-fill dot"></i>{{ ucfirst($item->estado) }}
                        </span>
                    </td>
                    <td class="td-end">
                        <a href="{{ route('almacen-items.show', $item) }}" class="btn btn-sm btn-secondary" title="Ver"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('almacen-items.edit', $item) }}" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                        <a href="{{ route('almacen-kardex.index', ['almacen_item_id' => $item->id]) }}" class="btn btn-sm btn-info" title="Kardex"><i class="bi bi-journal-text"></i></a>
                        @if($item->estado === 'activo')
                        <form action="{{ route('almacen-items.destroy', $item) }}" method="POST" style="display:inline"
                            onsubmit="return confirm('¿Desactivar este ítem?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-icon" title="Desactivar"><i class="bi bi-x-lg"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding:40px">
                        <i class="bi bi-box-seam" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4"></i>
                        No hay ítems registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $items->links() }}
@endsection
