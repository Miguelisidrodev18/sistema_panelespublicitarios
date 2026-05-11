@extends('layouts.app')

@section('title', 'Nuevo Panel Tradicional')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('paneles-tradicionales.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Nuevo Panel Tradicional</h5>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('paneles-tradicionales.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Código</label>
                <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control"
                    placeholder="Ej: PT-001">
            </div>
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                    class="form-control @error('nombre') is-invalid @enderror" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Dirección / Ubicación</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control"
                    placeholder="Av. Ejemplo 123, Ciudad">
            </div>
            <div class="col-md-4">
                <label class="form-label">N° de caras</label>
                <input type="number" name="caras" value="{{ old('caras', 1) }}" class="form-control" min="1">
                <div class="form-text">Cantidad de caras visibles del panel.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Latitud</label>
                <input type="number" name="lat" value="{{ old('lat') }}" class="form-control" step="any"
                    placeholder="-25.2867">
            </div>
            <div class="col-md-4">
                <label class="form-label">Longitud</label>
                <input type="number" name="lng" value="{{ old('lng') }}" class="form-control" step="any"
                    placeholder="-57.6470">
            </div>
            <div class="col-12">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" accept="image/*" class="form-control">
                <div class="form-text">Máximo 5 MB. Formatos: JPG, PNG, WEBP.</div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('paneles-tradicionales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Crear Panel
        </button>
    </div>
</form>
</div>
</div>
@endsection
