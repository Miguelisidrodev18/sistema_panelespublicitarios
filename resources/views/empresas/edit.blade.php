@extends('layouts.app')

@section('title', 'Editar Empresa')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Editar: {{ $empresa->nombre }}</h5>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('empresas.update', $empresa) }}" method="POST">
    @csrf @method('PUT')

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Información de la empresa</div>
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre / Razón social <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}"
                    class="form-control @error('nombre') is-invalid @enderror" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Encargado / Contacto</label>
                <input type="text" name="encargado" value="{{ old('encargado', $empresa->encargado) }}" class="form-control">
            </div>
            <div class="col-auto">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                        {{ old('activo', $empresa->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Empresa activa</label>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Servicios contratados</div>
        <div class="card-body">
            <div class="row g-2 mb-3">
                @foreach(['panel_digital' => 'Panel Digital', 'panel_tradicional' => 'Panel Tradicional', 'marketing_digital' => 'Marketing Digital'] as $field => $label)
                <div class="col-auto">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="{{ $field }}" value="1" id="{{ $field }}"
                            {{ old($field, $empresa->$field) ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                    </div>
                </div>
                @endforeach
            </div>
            <label class="form-label">Otros servicios</label>
            <textarea name="otros_servicios" class="form-control" rows="2">{{ old('otros_servicios', $empresa->otros_servicios) }}</textarea>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
    </div>
</form>

</div>
</div>
@endsection
