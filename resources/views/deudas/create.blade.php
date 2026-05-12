@extends('layouts.app')

@section('title', 'Nueva Deuda')
@section('subtitle', 'Registrar una nueva deuda')

@section('content')
<div class="form-card" style="max-width:700px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('deudas.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nueva Deuda</div>
    </div>
</div>

<form action="{{ route('deudas.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header"><span><i class="bi bi-exclamation-triangle"></i>Datos de la deuda</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Acreedor <span class="req">*</span></label>
                    <input type="text" name="acreedor" value="{{ old('acreedor') }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-select">
                        <option value="baja" {{ old('prioridad') === 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ old('prioridad','media') === 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ old('prioridad') === 'alta' ? 'selected' : '' }}>Alta</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Concepto <span class="req">*</span></label>
                    <input type="text" name="concepto" value="{{ old('concepto') }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Monto (S/.) <span class="req">*</span></label>
                    <input type="number" name="monto" value="{{ old('monto') }}" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha deuda <span class="req">*</span></label>
                    <input type="date" name="fecha_deuda" value="{{ old('fecha_deuda', date('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha vencimiento</label>
                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" class="form-control" rows="3">{{ old('notas') }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('deudas.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Registrar Deuda</button>
    </div>
</form>
</div>
@endsection
