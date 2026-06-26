@extends('layouts.app')

@section('title', 'Editar Proveedor')
@section('subtitle', 'Modificar datos del proveedor')

@section('content')
<div class="form-card" style="max-width:700px">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('proveedores.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar Proveedor</div>
    </div>
</div>

<form action="{{ route('proveedores.update', $proveedor) }}" method="POST">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header"><span><i class="bi bi-truck"></i>Datos del proveedor</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Razón Social <span class="req">*</span></label>
                    <input type="text" name="razon_social" value="{{ old('razon_social', $proveedor->razon_social) }}" class="form-control @error('razon_social') is-invalid @enderror" required>
                    @error('razon_social')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">RUC</label>
                    <input type="text" name="ruc" value="{{ old('ruc', $proveedor->ruc) }}" class="form-control @error('ruc') is-invalid @enderror" maxlength="11">
                    @error('ruc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $proveedor->direccion) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $proveedor->email) }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Persona de contacto</label>
                    <input type="text" name="contacto" value="{{ old('contacto', $proveedor->contacto) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rubro</label>
                    <input type="text" name="rubro" value="{{ old('rubro', $proveedor->rubro) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $proveedor->observaciones) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="activo" {{ old('estado', $proveedor->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $proveedor->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Actualizar Proveedor</button>
    </div>
</form>
</div>
@endsection
