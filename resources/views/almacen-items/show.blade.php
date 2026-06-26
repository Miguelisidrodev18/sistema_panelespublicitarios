@extends('layouts.app')

@section('title', 'Detalle de Ítem')
@section('subtitle', $item->nombre)

@section('content')
<div class="form-card" style="max-width:900px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('almacen-items.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">{{ $item->nombre }}</div>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('almacen-kardex.index', ['almacen_item_id' => $item->id]) }}" class="btn btn-info"><i class="bi bi-journal-text"></i>Ver Kardex</a>
        <a href="{{ route('almacen-items.edit', $item) }}" class="btn btn-warning"><i class="bi bi-pencil"></i>Editar</a>
    </div>
</div>

<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span><i class="bi bi-box-seam"></i>Información del ítem</span>
        <span class="badge badge-{{ $item->estado === 'activo' ? 'success' : 'gray' }}">
            <i class="bi bi-circle-fill dot"></i>{{ ucfirst($item->estado) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="detail-label">Nombre</div>
                <div class="detail-value">{{ $item->nombre }}</div>
            </div>
            <div class="col-md-3">
                <div class="detail-label">Código</div>
                <div class="detail-value"><code>{{ $item->codigo ?? '—' }}</code></div>
            </div>
            <div class="col-md-3">
                <div class="detail-label">Tipo</div>
                <div class="detail-value">
                    @php $badgeColors = ['maquina' => 'info', 'herramienta' => 'warning', 'indumentaria' => 'purple', 'materiales' => 'teal']; @endphp
                    <span class="badge badge-{{ $badgeColors[$item->tipo] ?? 'gray' }}">{{ $item->tipo_label }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Marca</div>
                <div class="detail-value">{{ $item->marca ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Serie</div>
                <div class="detail-value">{{ $item->serie ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Año de compra</div>
                <div class="detail-value">{{ $item->anio_compra ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Almacén</div>
                <div class="detail-value">{{ $item->almacen->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Proveedor</div>
                <div class="detail-value">{{ $item->proveedor->razon_social ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Stock Actual</div>
                <div class="detail-value" style="font-size:18px;font-weight:700;color:var(--primary)">
                    {{ number_format($item->stock_actual, $item->tipo === 'materiales' ? 2 : 0) }} {{ $item->unidad_medida }}
                </div>
            </div>
        </div>
    </div>
</div>

@if($item->movimientos->count())
<div class="card">
    <div class="card-header">
        <span><i class="bi bi-journal-text"></i>Últimos movimientos</span>
        <a href="{{ route('almacen-kardex.index', ['almacen_item_id' => $item->id]) }}" style="font-size:12px">Ver todos</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Saldo</th>
                    <th>Responsable</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->movimientos as $mov)
                <tr>
                    <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger' }}">
                            <i class="bi bi-{{ $mov->tipo_movimiento === 'entrada' ? 'arrow-down' : 'arrow-up' }}"></i>
                            {{ ucfirst($mov->tipo_movimiento) }}
                        </span>
                    </td>
                    <td class="fw-600">{{ number_format($mov->cantidad, 2) }} {{ $item->unidad_medida }}</td>
                    <td class="fw-600">{{ number_format($mov->saldo, 2) }}</td>
                    <td>{{ $mov->responsable->nombre_completo ?? '—' }}</td>
                    <td>{{ $mov->detalle ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

</div>
@endsection
