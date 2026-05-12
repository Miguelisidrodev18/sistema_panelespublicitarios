@extends('layouts.app')

@section('title', 'Nueva Empresa')
@section('subtitle', 'Registrar una nueva empresa')

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('empresas.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nueva Empresa</div>
    </div>
</div>

<form action="{{ route('empresas.store') }}" method="POST">
    @csrf

    {{-- Búsqueda por RUC --}}
    <div class="card card-accent">
        <div class="card-header" style="background:#fff;color:var(--text-dark);border-bottom:1px solid var(--border)">
            <span><i class="bi bi-search" style="color:var(--primary);margin-right:8px"></i>Buscar por RUC (SUNAT)</span>
            <span class="text-muted" style="font-size:11px">opcional</span>
        </div>
        <div class="card-body">
            <div class="flex flex-center gap-12">
                <div>
                    <input type="text" id="rucInput" class="form-control" placeholder="Ej: 20123456789"
                        maxlength="11" inputmode="numeric" style="width:220px">
                    <div class="form-hint">11 dígitos sin guiones</div>
                </div>
                <button type="button" id="btnBuscarRuc" class="btn btn-outline" onclick="buscarRuc()">
                    <i class="bi bi-search"></i>Buscar
                </button>
            </div>
            <div id="rucResult" style="display:none;margin-top:14px"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span><i class="bi bi-building"></i>Información de la empresa</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre / Razón social <span class="req">*</span></label>
                    <input type="text" name="nombre" id="nombreEmpresa" value="{{ old('nombre') }}"
                        class="form-control @error('nombre') is-invalid @enderror" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Encargado / Contacto</label>
                    <input type="text" name="encargado" value="{{ old('encargado') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" value="{{ old('correo') }}" class="form-control @error('correo') is-invalid @enderror" placeholder="empresa@ejemplo.com">
                    @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Celular / Teléfono</label>
                    <input type="text" name="celular" value="{{ old('celular') }}" class="form-control" placeholder="Ej: 987654321">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span><i class="bi bi-tags"></i>Servicios contratados</span></div>
        <div class="card-body">
            <div class="chip-group" style="margin-bottom:16px">
                <label class="toggle-switch">
                    <input type="checkbox" name="panel_digital" value="1" {{ old('panel_digital') ? 'checked' : '' }}>
                    <span><i class="bi bi-display" style="margin-right:4px"></i>Panel Digital</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="panel_tradicional" value="1" {{ old('panel_tradicional') ? 'checked' : '' }}>
                    <span><i class="bi bi-signpost-2" style="margin-right:4px"></i>Panel Tradicional</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="marketing_digital" value="1" {{ old('marketing_digital') ? 'checked' : '' }}>
                    <span><i class="bi bi-megaphone" style="margin-right:4px"></i>Marketing Digital</span>
                </label>
            </div>
            <label class="form-label">Otros servicios</label>
            <textarea name="otros_servicios" class="form-control" rows="2"
                placeholder="Describe otros servicios si aplica...">{{ old('otros_servicios') }}</textarea>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Crear Empresa</button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('rucInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); buscarRuc(); }
});

function buscarRuc() {
    var ruc = document.getElementById('rucInput').value.trim();
    var resultDiv = document.getElementById('rucResult');
    var btn = document.getElementById('btnBuscarRuc');

    if (!/^\d{11}$/.test(ruc)) {
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill"></i><span>El RUC debe tener exactamente 11 dígitos.</span></div>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i>Buscando...';
    resultDiv.style.display = 'none';

    fetch('/sunat/ruc/' + ruc)
    .then(function (res) {
        if (!res.ok) throw new Error('No encontrado');
        return res.json();
    })
    .then(function (data) {
        if (!data.nombre) throw new Error('Sin datos');
        document.getElementById('nombreEmpresa').value = data.nombre;
        var dir = [data.direccion, data.distrito, data.provincia, data.departamento].filter(function(v) { return v && v !== '-'; }).join(', ');
        resultDiv.style.display = 'block';
        resultDiv.innerHTML =
            '<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i>' +
            '<div><strong>' + data.nombre + '</strong>' +
            '<div style="font-size:12px;margin-top:4px">RUC: ' + ruc +
            ' · Estado: <span class="badge badge-success">' + (data.estado || '—') + '</span>' +
            ' · Condición: <span class="badge badge-' + (data.condicion === 'HABIDO' ? 'success' : 'danger') + '">' + (data.condicion || '—') + '</span>' +
            (dir ? '<br>Dirección: ' + dir : '') +
            '</div></div></div>';
    })
    .catch(function () {
        resultDiv.style.display = 'block';
        resultDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle-fill"></i><span>RUC no encontrado. Completá los datos manualmente.</span></div>';
    })
    .finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search"></i>Buscar';
    });
}
</script>
@endpush
