@extends('layouts.app')

@section('title', 'Editar Empresa')
@section('subtitle', $empresa->nombre)

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('empresas.show', $empresa) }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar: {{ $empresa->nombre }}</div>
    </div>
</div>

<form action="{{ route('empresas.update', $empresa) }}" method="POST">
    @csrf @method('PUT')

    <div class="card">
        <div class="card-header"><span><i class="bi bi-building"></i>Información de la empresa</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre / Razón social <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}"
                        class="form-control @error('nombre') is-invalid @enderror" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Encargado / Contacto</label>
                    <input type="text" name="encargado" value="{{ old('encargado', $empresa->encargado) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" value="{{ old('correo', $empresa->correo) }}" class="form-control @error('correo') is-invalid @enderror" placeholder="empresa@ejemplo.com">
                    @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Celular / Teléfono</label>
                    <input type="text" name="celular" value="{{ old('celular', $empresa->celular) }}" class="form-control" placeholder="Ej: 987654321">
                </div>
                <div class="col-auto">
                    <label class="toggle-switch">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $empresa->activo) ? 'checked' : '' }}>
                        <span>Empresa activa</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span><i class="bi bi-tags"></i>Servicios contratados</span></div>
        <div class="card-body">
            <div class="chip-group" style="margin-bottom:16px">
                @foreach(['panel_digital' => 'Panel Digital', 'panel_tradicional' => 'Panel Tradicional', 'marketing_digital' => 'Marketing Digital'] as $field => $label)
                <label class="toggle-switch">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $empresa->$field) ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                </label>
                @endforeach
            </div>
            <label class="form-label">Otros servicios</label>
            <textarea name="otros_servicios" class="form-control" rows="2">{{ old('otros_servicios', $empresa->otros_servicios) }}</textarea>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
    </div>
</form>
</div>
@endsection
