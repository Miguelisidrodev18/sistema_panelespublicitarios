@extends('layouts.app')

@section('title', 'Nuevo Servicio')
@section('subtitle', 'Registrar un nuevo servicio')

@section('content')
<div class="form-card">
<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('servicios.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nuevo Servicio</div>
    </div>
</div>

<form action="{{ route('servicios.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header"><span><i class="bi bi-box-seam"></i>Datos del servicio</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Monto base (S/.)</label>
                    <input type="number" name="monto" value="{{ old('monto', 0) }}" class="form-control" step="0.01" min="0">
                    <div class="form-hint">0 para servicios libres/gratuitos.</div>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2" maxlength="500">{{ old('descripcion') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Icono Bootstrap</label>
                    <input type="text" name="icono" value="{{ old('icono', 'box') }}" class="form-control" placeholder="Ej: megaphone, display, camera">
                    <div class="form-hint">Nombre del ícono de Bootstrap Icons.</div>
                </div>
                <div class="col-auto">
                    <label class="toggle-switch">
                        <input type="checkbox" name="activo" value="1" checked>
                        <span>Servicio activo</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('servicios.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Crear Servicio</button>
    </div>
</form>
</div>
@endsection
