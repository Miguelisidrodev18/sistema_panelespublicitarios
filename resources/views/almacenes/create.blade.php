@extends('layouts.app')

@section('title', 'Nuevo Almacén')
@section('subtitle', 'Registrar un nuevo almacén')

@section('content')
<div class="form-card" style="max-width:650px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('almacenes.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nuevo Almacén</div>
    </div>
</div>

<form action="{{ route('almacenes.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header"><span><i class="bi bi-building"></i>Datos del almacén</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control" placeholder="Ej: ALM-01">
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Responsable</label>
                    <input type="text" name="responsable" value="{{ old('responsable') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="activo" selected>Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-6" style="display:flex;align-items:flex-end">
                    <label class="toggle-switch">
                        <input type="checkbox" name="es_principal" value="1" {{ old('es_principal') ? 'checked' : '' }}>
                        <span>Almacén principal</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('almacenes.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Registrar Almacén</button>
    </div>
</form>
</div>
@endsection
