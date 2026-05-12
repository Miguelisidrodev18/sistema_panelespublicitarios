@extends('layouts.app')

@section('title', 'Nuevo Panel Tradicional')
@section('subtitle', 'Registrar un nuevo panel tradicional')

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('paneles-tradicionales.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nuevo Panel Tradicional</div>
    </div>
</div>

@if($errors->any())
<div class="card card-accent" style="border-color:#FCA5A5;margin-bottom:16px">
    <div class="card-body" style="background:#FEF2F2;color:var(--primary)">
        <ul style="margin:0;padding-left:16px;font-size:13px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('paneles-tradicionales.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header"><span><i class="bi bi-signpost-2"></i>Datos del panel</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control" placeholder="Ej: PT-001">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección / Ubicación</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control" placeholder="Av. Ejemplo 123, Ciudad">
                </div>
                <div class="col-md-4">
                    <label class="form-label">N° de caras</label>
                    <input type="number" name="caras" value="{{ old('caras', 1) }}" class="form-control" min="1">
                    <div class="form-hint">Cantidad de caras visibles del panel.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Medidas</label>
                    <input type="text" name="medidas" value="{{ old('medidas') }}" class="form-control" placeholder="Ej: 8x4m, 12x6m">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gramaje de lonas</label>
                    <input type="text" name="gramaje_lonas" value="{{ old('gramaje_lonas') }}" class="form-control" placeholder="Ej: 440gr/m²">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Costo de producción (S/.)</label>
                    <input type="number" name="costo_produccion" value="{{ old('costo_produccion') }}" class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Latitud</label>
                    <input type="number" name="lat" value="{{ old('lat') }}" class="form-control" step="any" placeholder="-25.2867">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Longitud</label>
                    <input type="number" name="lng" value="{{ old('lng') }}" class="form-control" step="any" placeholder="-57.6470">
                </div>
                <div class="col-12">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" accept="image/*" class="form-control">
                    <div class="form-hint">Máximo 5 MB. Formatos: JPG, PNG, WEBP.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('paneles-tradicionales.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Crear Panel</button>
    </div>
</form>
</div>
@endsection
