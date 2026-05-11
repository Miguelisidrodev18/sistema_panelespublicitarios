@extends('layouts.app')

@section('title', 'Editar Almacén')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('almacenes.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Editar: {{ $almacen->nombre }}</h5>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('almacenes.update', $almacen) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $almacen->nombre) }}"
                    class="form-control @error('nombre') is-invalid @enderror" required>
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
                    <option value="activo"   {{ old('estado', $almacen->estado) === 'activo'   ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado', $almacen->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="es_principal" value="1"
                        id="es_principal" {{ old('es_principal', $almacen->es_principal) ? 'checked' : '' }}>
                    <label class="form-check-label" for="es_principal">Almacén principal</label>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('almacenes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
    </div>
</form>

</div>
</div>
@endsection
