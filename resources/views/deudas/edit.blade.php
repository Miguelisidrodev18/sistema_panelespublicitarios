@extends('layouts.app')

@section('title', 'Editar Deuda')
@section('subtitle', $deuda->acreedor)

@section('content')
<div class="form-card" style="max-width:700px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('deudas.show', $deuda) }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar Deuda</div>
    </div>
</div>

<form action="{{ route('deudas.update', $deuda) }}" method="POST">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header"><span><i class="bi bi-exclamation-triangle"></i>Datos de la deuda</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Acreedor <span class="req">*</span></label>
                    <input type="text" name="acreedor" value="{{ old('acreedor', $deuda->acreedor) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-select">
                        @foreach(['baja','media','alta'] as $p)
                        <option value="{{ $p }}" {{ old('prioridad', $deuda->prioridad) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Concepto <span class="req">*</span></label>
                    <input type="text" name="concepto" value="{{ old('concepto', $deuda->concepto) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Monto (S/.) <span class="req">*</span></label>
                    <input type="number" name="monto" value="{{ old('monto', $deuda->monto) }}" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha deuda <span class="req">*</span></label>
                    <input type="date" name="fecha_deuda" value="{{ old('fecha_deuda', $deuda->fecha_deuda->format('Y-m-d')) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha vencimiento</label>
                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $deuda->fecha_vencimiento?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        @foreach(['pendiente','pagada','cancelada'] as $e)
                        <option value="{{ $e }}" {{ old('estado', $deuda->estado) === $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notas</label>
                    <textarea name="notas" class="form-control" rows="3">{{ old('notas', $deuda->notas) }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('deudas.show', $deuda) }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
    </div>
</form>
</div>
@endsection
