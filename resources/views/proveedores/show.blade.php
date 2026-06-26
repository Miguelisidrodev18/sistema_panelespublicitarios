@extends('layouts.app')

@section('title', 'Proveedor')
@section('subtitle', $proveedor->razon_social)

@section('content')
<div class="form-card" style="max-width:800px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('proveedores.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">{{ $proveedor->razon_social }}</div>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('proveedores.edit', $proveedor) }}" class="btn btn-warning"><i class="bi bi-pencil"></i>Editar</a>
    </div>
</div>

<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span><i class="bi bi-truck"></i>Datos del proveedor</span>
        <span class="badge badge-{{ $proveedor->estado === 'activo' ? 'success' : 'gray' }}">
            <i class="bi bi-circle-fill dot"></i>{{ ucfirst($proveedor->estado) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="detail-label">Razón Social</div>
                <div class="detail-value">{{ $proveedor->razon_social }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">RUC</div>
                <div class="detail-value"><code>{{ $proveedor->ruc ?? '—' }}</code></div>
            </div>
            <div class="col-12">
                <div class="detail-label">Dirección</div>
                <div class="detail-value">{{ $proveedor->direccion ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Teléfono</div>
                <div class="detail-value">{{ $proveedor->telefono ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Email</div>
                <div class="detail-value">{{ $proveedor->email ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="detail-label">Persona de contacto</div>
                <div class="detail-value">{{ $proveedor->contacto ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="detail-label">Rubro</div>
                <div class="detail-value">{{ $proveedor->rubro ?? '—' }}</div>
            </div>
            @if($proveedor->observaciones)
            <div class="col-12">
                <div class="detail-label">Observaciones</div>
                <div class="detail-value">{{ $proveedor->observaciones }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($proveedor->items->count())
<div class="card">
    <div class="card-header"><span><i class="bi bi-box-seam"></i>Ítems vinculados ({{ $proveedor->items->count() }})</span></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Almacén</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proveedor->items as $item)
                <tr>
                    <td class="fw-600">{{ $item->nombre }}</td>
                    <td><code>{{ $item->codigo ?? '—' }}</code></td>
                    <td>{{ $item->tipo_label }}</td>
                    <td>{{ $item->almacen->nombre ?? '—' }}</td>
                    <td>{{ number_format($item->stock_actual, $item->tipo === 'materiales' ? 2 : 0) }} {{ $item->unidad_medida }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

</div>
@endsection
