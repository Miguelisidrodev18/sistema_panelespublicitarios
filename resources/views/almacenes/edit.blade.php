@extends('layouts.app')

@section('title', 'Editar Almacén')
@section('subtitle', $almacen->nombre)

@section('content')
<div class="form-card" style="max-width:650px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('almacenes.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar: {{ $almacen->nombre }}</div>
    </div>
</div>

<form action="{{ route('almacenes.update', $almacen) }}" method="POST">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header"><span><i class="bi bi-building"></i>Datos del almacén</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $almacen->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $almacen->codigo) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $almacen->direccion) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $almacen->telefono) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Responsable</label>
                    <input type="text" name="responsable" value="{{ old('responsable', $almacen->responsable) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="activo" {{ old('estado', $almacen->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $almacen->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-6" style="display:flex;align-items:flex-end">
                    <label class="toggle-switch">
                        <input type="checkbox" name="es_principal" value="1" {{ old('es_principal', $almacen->es_principal) ? 'checked' : '' }}>
                        <span>Almacén principal</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('almacenes.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
    </div>
</form>
</div>
@endsection
