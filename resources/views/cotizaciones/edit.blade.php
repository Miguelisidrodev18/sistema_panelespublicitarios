@extends('layouts.app')

@section('title', 'Editar Cotización')
@section('subtitle', $cotizacion->numero)

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar Cotización {{ $cotizacion->numero }}</div>
    </div>
</div>

@if($errors->any())
<div class="card card-accent" style="border-color:#FCA5A5;margin-bottom:16px">
    <div class="card-body" style="background:#FEF2F2;color:var(--primary);font-size:13px">
        <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

<form action="{{ route('cotizaciones.update', $cotizacion) }}" method="POST">
    @csrf @method('PUT')

    <div class="card">
        <div class="card-header"><span><i class="bi bi-person"></i>Datos del cliente</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre del cliente <span class="req">*</span></label>
                    <input type="text" name="cliente_nombre" value="{{ old('cliente_nombre', $cotizacion->cliente_nombre) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        @foreach(['pendiente'=>'Pendiente','aprobada'=>'Aprobada','rechazada'=>'Rechazada','convertida'=>'Convertida'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('estado', $cotizacion->estado) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
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
    </div>

    <div class="card">
        <div class="card-header"><span><i class="bi bi-file-invoice-dollar"></i>Detalles de la propuesta</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo de servicio</label>
                    <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato', $cotizacion->tipo_contrato) }}" class="form-control" list="tipos_contrato">
                    <datalist id="tipos_contrato"><option value="Panel Digital"><option value="Panel Tradicional"><option value="Marketing Digital"><option value="Mixto"></datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Monto propuesto (S/.)</label>
                    <input type="number" name="monto_propuesto" value="{{ old('monto_propuesto', $cotizacion->monto_propuesto) }}" class="form-control" step="1" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha cotización</label>
                    <input type="date" name="fecha_cotizacion" value="{{ old('fecha_cotizacion', $cotizacion->fecha_cotizacion?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vence el</label>
                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $cotizacion->fecha_vencimiento?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas / Descripción</label>
                    <textarea name="notas" class="form-control" rows="3">{{ old('notas', $cotizacion->notas) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
    </div>
</form>
</div>
@endsection
