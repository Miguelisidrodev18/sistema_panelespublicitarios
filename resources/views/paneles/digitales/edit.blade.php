@extends('layouts.app')

@section('title', 'Editar Panel Digital')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="d-flex align-items-center mb-3">
    <a href="{{ route('paneles-digitales.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Editar: {{ $panelDigital->nombre }}</h5>
</div>
<form action="{{ route('paneles-digitales.update', $panelDigital) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Código</label>
                <input type="text" name="codigo" value="{{ old('codigo', $panelDigital->codigo) }}" class="form-control">
            </div>
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $panelDigital->nombre) }}" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $panelDigital->direccion) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Medidas</label>
                <input type="text" name="medidas" value="{{ old('medidas', $panelDigital->medidas) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resolución</label>
                <input type="text" name="resolucion" value="{{ old('resolucion', $panelDigital->resolucion) }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Orientación</label>
                <select name="orientacion" class="form-select">
                    <option value="">-</option>
                    @foreach(['horizontal','vertical'] as $o)
                    <option value="{{ $o }}" {{ old('orientacion', $panelDigital->orientacion) === $o ? 'selected' : '' }}>{{ ucfirst($o) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tandas</label>
                <input type="number" name="tandas" value="{{ old('tandas', $panelDigital->tandas) }}" class="form-control" min="1">
            </div>
            <div class="col-md-6">
                <label class="form-label">Latitud</label>
                <input type="number" name="lat" value="{{ old('lat', $panelDigital->lat) }}" class="form-control" step="any">
            </div>
            <div class="col-md-6">
                <label class="form-label">Longitud</label>
                <input type="number" name="lng" value="{{ old('lng', $panelDigital->lng) }}" class="form-control" step="any">
            </div>
            <div class="col-12">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" accept="image/*" class="form-control">
                @if($panelDigital->foto)
                    <div class="mt-2"><img src="{{ Storage::url($panelDigital->foto) }}" height="80" class="rounded"></div>
                @endif
            </div>
            <div class="col-auto">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo"
                        {{ old('activo', $panelDigital->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Panel activo</label>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('paneles-digitales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
        </button>
    </div>
</form>
</div>
</div>
@endsection
