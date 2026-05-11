@extends('layouts.app')

@section('title', 'Nuevo Panel Digital')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('paneles-digitales.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Nuevo Panel Digital</h5>
</div>
<form action="{{ route('paneles-digitales.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Código</label>
                <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control">
            </div>
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Medidas</label>
                <input type="text" name="medidas" value="{{ old('medidas') }}" class="form-control" placeholder="Ej: 6x3m">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resolución</label>
                <input type="text" name="resolucion" value="{{ old('resolucion') }}" class="form-control" placeholder="Ej: 1920x1080">
            </div>
            <div class="col-md-2">
                <label class="form-label">Orientación</label>
                <select name="orientacion" class="form-select">
                    <option value="">-</option>
                    <option value="horizontal" {{ old('orientacion') === 'horizontal' ? 'selected' : '' }}>Horizontal</option>
                    <option value="vertical" {{ old('orientacion') === 'vertical' ? 'selected' : '' }}>Vertical</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tandas</label>
                <input type="number" name="tandas" value="{{ old('tandas') }}" class="form-control" min="1">
            </div>
            <div class="col-md-6">
                <label class="form-label">Latitud</label>
                <input type="number" name="lat" value="{{ old('lat') }}" class="form-control" step="any">
            </div>
            <div class="col-md-6">
                <label class="form-label">Longitud</label>
                <input type="number" name="lng" value="{{ old('lng') }}" class="form-control" step="any">
            </div>
            <div class="col-12">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" accept="image/*" class="form-control">
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('paneles-digitales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>Crear Panel
        </button>
    </div>
</form>
</div>
</div>
@endsection
