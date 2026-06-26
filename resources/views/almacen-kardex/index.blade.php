@extends('layouts.app')

@section('title', 'Kardex')
@section('subtitle', 'Control de herramientas, material y equipo de personal')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Control de herramientas, material y equipo de personal</div>
    </div>
</div>

{{-- Filtros --}}
<div class="card" style="margin-bottom:16px">
    <div class="card-header"><span><i class="bi bi-funnel"></i>Filtros</span></div>
    <div class="card-body">
        <form method="GET" action="{{ route('almacen-kardex.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Ítem <span class="req">*</span></label>
                    <select name="almacen_item_id" class="form-select" required>
                        <option value="">Seleccionar ítem</option>
                        @foreach($items as $it)
                            <option value="{{ $it->id }}" {{ request('almacen_item_id') == $it->id ? 'selected' : '' }}>
                                {{ $it->nombre }} ({{ $it->almacen->nombre ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Proyecto u obra</label>
                    <select name="panel_digital_id" class="form-select">
                        <option value="">Todos los proyectos</option>
                        <optgroup label="Paneles Digitales">
                            @foreach($panelesDigitales as $pd)
                                <option value="{{ $pd->id }}" {{ request('panel_digital_id') == $pd->id ? 'selected' : '' }}>{{ $pd->nombre }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    <select name="panel_ubicacion_id" class="form-select" style="margin-top:6px">
                        <option value="">Panel tradicional</option>
                        <optgroup label="Paneles Tradicionales">
                            @foreach($panelesUbicaciones as $pu)
                                <option value="{{ $pu->id }}" {{ request('panel_ubicacion_id') == $pu->id ? 'selected' : '' }}>{{ $pu->nombre }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Responsable</label>
                    <select name="responsable_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}" {{ request('responsable_id') == $u->id ? 'selected' : '' }}>{{ $u->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control">
                </div>
                <div class="col-md-6" style="display:flex;align-items:flex-end;gap:8px">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i>Consultar</button>
                    @if($itemSeleccionado)
                    <a href="{{ route('almacen-kardex.imprimir', request()->query()) }}" target="_blank" class="btn btn-secondary"><i class="bi bi-printer"></i>Imprimir</a>
                    <a href="{{ route('almacen-kardex.pdf', request()->query()) }}" class="btn btn-danger"><i class="bi bi-file-pdf"></i>PDF</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabla Kardex --}}
@if($itemSeleccionado)
<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <span><i class="bi bi-journal-text"></i>KARDEX BÚHO — {{ $itemSeleccionado->nombre }}</span>
        <span class="badge badge-{{ ['maquina' => 'info', 'herramienta' => 'warning', 'indumentaria' => 'purple', 'materiales' => 'teal'][$itemSeleccionado->tipo] ?? 'gray' }}">
            {{ $itemSeleccionado->tipo_label }}
        </span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Detalle</th>
                    <th class="text-center">Entradas Cant.</th>
                    <th class="text-center">Salida Cant.</th>
                    <th class="text-center">Saldo</th>
                    <th>Responsable</th>
                    <th>Proyecto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $mov)
                <tr>
                    <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                    <td>{{ $mov->detalle ?? $itemSeleccionado->nombre }}</td>
                    <td class="text-center fw-600" style="color:var(--success)">
                        {{ $mov->tipo_movimiento === 'entrada' ? number_format($mov->cantidad, 2) . ' ' . $itemSeleccionado->unidad_medida : '' }}
                    </td>
                    <td class="text-center fw-600" style="color:var(--danger)">
                        {{ $mov->tipo_movimiento === 'salida' ? number_format($mov->cantidad, 2) . ' ' . $itemSeleccionado->unidad_medida : '' }}
                    </td>
                    <td class="text-center fw-700">{{ number_format($mov->saldo, 2) }}</td>
                    <td>{{ $mov->responsable->nombre_completo ?? '—' }}</td>
                    <td>{{ $mov->proyecto_nombre ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding:30px">
                        No hay movimientos registrados para este ítem
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Formulario de nuevo movimiento --}}
<div class="card">
    <div class="card-header">
        <span><i class="bi bi-plus-circle"></i>Registrar Movimiento</span>
    </div>
    <div class="card-body">
        <form action="{{ route('almacen-kardex.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Ítem <span class="req">*</span></label>
                    <select name="almacen_item_id" class="form-select @error('almacen_item_id') is-invalid @enderror" required>
                        <option value="">Seleccionar ítem</option>
                        @foreach($items as $it)
                            <option value="{{ $it->id }}" {{ old('almacen_item_id', $itemSeleccionado->id ?? '') == $it->id ? 'selected' : '' }}>
                                {{ $it->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('almacen_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo <span class="req">*</span></label>
                    <select name="tipo_movimiento" class="form-select @error('tipo_movimiento') is-invalid @enderror" required>
                        <option value="entrada" {{ old('tipo_movimiento', $accion) === 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida" {{ old('tipo_movimiento', $accion) === 'salida' ? 'selected' : '' }}>Salida</option>
                    </select>
                    @error('tipo_movimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cantidad <span class="req">*</span></label>
                    <input type="number" name="cantidad" value="{{ old('cantidad') }}" class="form-control @error('cantidad') is-invalid @enderror" step="0.01" min="0.01" required>
                    @error('cantidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha <span class="req">*</span></label>
                    <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" class="form-control @error('fecha') is-invalid @enderror" required>
                    @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-5">
                    <label class="form-label">Detalle</label>
                    <input type="text" name="detalle" value="{{ old('detalle') }}" class="form-control" placeholder="Descripción del movimiento">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Responsable</label>
                    <select name="responsable_id" class="form-select">
                        <option value="">Seleccionar</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}" {{ old('responsable_id') == $u->id ? 'selected' : '' }}>{{ $u->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Proyecto (Panel Digital)</label>
                    <select name="panel_digital_id" class="form-select">
                        <option value="">Ninguno</option>
                        @foreach($panelesDigitales as $pd)
                            <option value="{{ $pd->id }}" {{ old('panel_digital_id') == $pd->id ? 'selected' : '' }}>{{ $pd->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Proyecto (Panel Tradicional)</label>
                    <select name="panel_ubicacion_id" class="form-select">
                        <option value="">Ninguno</option>
                        @foreach($panelesUbicaciones as $pu)
                            <option value="{{ $pu->id }}" {{ old('panel_ubicacion_id') == $pu->id ? 'selected' : '' }}>{{ $pu->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor_id" class="form-select">
                        <option value="">Ninguno</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ old('proveedor_id') == $prov->id ? 'selected' : '' }}>{{ $prov->razon_social }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Observaciones</label>
                    <input type="text" name="observaciones" value="{{ old('observaciones') }}" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Guardar Movimiento</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
