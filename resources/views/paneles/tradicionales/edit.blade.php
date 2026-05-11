@extends('layouts.app')

@section('title', 'Editar Panel: ' . $panelTradicional->nombre)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('paneles-tradicionales.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Editar: {{ $panelTradicional->nombre }}</h5>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('paneles-tradicionales.update', $panelTradicional) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-3">
            @if($panelTradicional->foto)
            <div class="col-12">
                <label class="form-label">Foto actual</label>
                <div>
                    <img src="{{ Storage::url($panelTradicional->foto) }}" style="height:120px;object-fit:cover;border-radius:8px">
                </div>
            </div>
            @endif
            <div class="col-md-4">
                <label class="form-label">Código</label>
                <input type="text" name="codigo" value="{{ old('codigo', $panelTradicional->codigo) }}" class="form-control">
            </div>
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $panelTradicional->nombre) }}"
                    class="form-control @error('nombre') is-invalid @enderror" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Dirección / Ubicación</label>
                <input type="text" name="direccion" value="{{ old('direccion', $panelTradicional->direccion) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">N° de caras</label>
                <input type="number" name="caras" value="{{ old('caras', $panelTradicional->caras) }}" class="form-control" min="1">
            </div>
            <div class="col-md-4">
                <label class="form-label">Latitud</label>
                <input type="number" name="lat" value="{{ old('lat', $panelTradicional->lat) }}" class="form-control" step="any">
            </div>
            <div class="col-md-4">
                <label class="form-label">Longitud</label>
                <input type="number" name="lng" value="{{ old('lng', $panelTradicional->lng) }}" class="form-control" step="any">
            </div>
            <div class="col-12">
                <label class="form-label">Nueva foto (opcional)</label>
                <input type="file" name="foto" accept="image/*" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="activo" class="form-select">
                    <option value="1" {{ old('activo', $panelTradicional->activo) ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ !old('activo', $panelTradicional->activo) ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('paneles-tradicionales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
    </div>
</form>
</div>
</div>
@endsection
