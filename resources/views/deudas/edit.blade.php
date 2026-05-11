@extends('layouts.app')

@section('title', 'Editar Deuda')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('deudas.show', $deuda) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Editar Deuda</h5>
</div>
<form action="{{ route('deudas.update', $deuda) }}" method="POST">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label">Acreedor <span class="text-danger">*</span></label>
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
                <label class="form-label">Concepto <span class="text-danger">*</span></label>
                <input type="text" name="concepto" value="{{ old('concepto', $deuda->concepto) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Monto (S/.) <span class="text-danger">*</span></label>
                <input type="number" name="monto" value="{{ old('monto', $deuda->monto) }}" class="form-control" step="0.01" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha deuda <span class="text-danger">*</span></label>
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
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('deudas.show', $deuda) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
    </div>
</form>
</div>
</div>
@endsection
