@extends('layouts.app')

@section('title', 'Editar Panel Tradicional')
@section('subtitle', $panelTradicional->nombre)

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('paneles-tradicionales.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar: {{ $panelTradicional->nombre }}</div>
    </div>
</div>

@if($errors->any())
<div class="card card-accent" style="border-color:#FCA5A5;margin-bottom:16px">
    <div class="card-body" style="background:#FEF2F2;color:var(--primary);font-size:13px">
        <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

<form action="{{ route('paneles-tradicionales.update', $panelTradicional) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header"><span><i class="bi bi-signpost-2"></i>Datos del panel</span></div>
        <div class="card-body">
            <div class="row g-3">
                @if($panelTradicional->foto)
                <div class="col-12">
                    <label class="form-label">Foto actual</label>
                    <div><img src="{{ Storage::url($panelTradicional->foto) }}" style="height:120px;object-fit:cover;border-radius:8px"></div>
                </div>
                @endif
                <div class="col-md-4">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $panelTradicional->codigo) }}" class="form-control">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $panelTradicional->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
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
                    <label class="form-label">Medidas</label>
                    <input type="text" name="medidas" value="{{ old('medidas', $panelTradicional->medidas) }}" class="form-control" placeholder="Ej: 8x4m">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gramaje de lonas</label>
                    <input type="text" name="gramaje_lonas" value="{{ old('gramaje_lonas', $panelTradicional->gramaje_lonas) }}" class="form-control" placeholder="Ej: 440gr/m²">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Costo de producción (S/.)</label>
                    <input type="number" name="costo_produccion" value="{{ old('costo_produccion', $panelTradicional->costo_produccion) }}" class="form-control" step="0.01" min="0">
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
    </div>
    <div class="action-bar">
        <a href="{{ route('paneles-tradicionales.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
    </div>
</form>
</div>
@endsection
