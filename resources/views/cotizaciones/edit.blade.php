@extends('layouts.app')

@section('title', 'Editar Cotización')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Editar Cotización {{ $cotizacion->numero }}</h5>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('cotizaciones.update', $cotizacion) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Datos del cliente</div>
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre del cliente <span class="text-danger">*</span></label>
                <input type="text" name="cliente_nombre" value="{{ old('cliente_nombre', $cotizacion->cliente_nombre) }}"
                    class="form-control @error('cliente_nombre') is-invalid @enderror" required>
                @error('cliente_nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="pendiente"  {{ old('estado', $cotizacion->estado) === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobada"   {{ old('estado', $cotizacion->estado) === 'aprobada'   ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada"  {{ old('estado', $cotizacion->estado) === 'rechazada'  ? 'selected' : '' }}>Rechazada</option>
                    <option value="convertida" {{ old('estado', $cotizacion->estado) === 'convertida' ? 'selected' : '' }}>Convertida</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Empresa del cliente</label>
                <input type="text" name="cliente_empresa" value="{{ old('cliente_empresa', $cotizacion->cliente_empresa) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="cliente_telefono" value="{{ old('cliente_telefono', $cotizacion->cliente_telefono) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Email</label>
                <input type="email" name="cliente_email" value="{{ old('cliente_email', $cotizacion->cliente_email) }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Detalles de la propuesta</div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Tipo de servicio</label>
                <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato', $cotizacion->tipo_contrato) }}"
                    class="form-control" list="tipos_contrato">
                <datalist id="tipos_contrato">
                    <option value="Panel Digital">
                    <option value="Panel Tradicional">
                    <option value="Marketing Digital">
                    <option value="Mixto">
                </datalist>
            </div>
            <div class="col-md-6">
                <label class="form-label">Monto propuesto (S/.)</label>
                <input type="number" name="monto_propuesto" value="{{ old('monto_propuesto', $cotizacion->monto_propuesto) }}"
                    class="form-control" step="1" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha cotización</label>
                <input type="date" name="fecha_cotizacion"
                    value="{{ old('fecha_cotizacion', $cotizacion->fecha_cotizacion?->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Vence el</label>
                <input type="date" name="fecha_vencimiento"
                    value="{{ old('fecha_vencimiento', $cotizacion->fecha_vencimiento?->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Notas / Descripción</label>
                <textarea name="notas" class="form-control" rows="3">{{ old('notas', $cotizacion->notas) }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
    </div>
</form>

</div>
</div>
@endsection
