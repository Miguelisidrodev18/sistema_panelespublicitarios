@extends('layouts.app')

@section('title', 'Nueva Empresa')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('empresas.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Nueva Empresa</h5>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('empresas.store') }}" method="POST">
    @csrf

    {{-- Búsqueda por RUC (SUNAT) --}}
    <div class="card border-0 shadow-sm mb-3" style="border-left:3px solid var(--primary) !important">
        <div class="card-header bg-white py-3 d-flex align-items-center gap-2">
            <i class="bi bi-search" style="color:var(--primary)"></i>
            <span class="fw-medium">Buscar por RUC (SUNAT)</span>
            <span class="text-muted small ms-1">— opcional, pre-rellena el nombre automáticamente</span>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2 align-items-start">
                <div style="width:220px">
                    <input type="text" id="rucInput" class="form-control" placeholder="Ej: 20123456789"
                        maxlength="11" inputmode="numeric">
                    <div class="form-text">11 dígitos sin guiones</div>
                </div>
                <button type="button" id="btnBuscarRuc" class="btn btn-outline-danger" onclick="buscarRuc()">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
            </div>
            <div id="rucResult" class="mt-3" style="display:none"></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Información de la empresa</div>
        <div class="card-body row g-3">
            <div class="col-md-8">
                <label class="form-label">Nombre / Razón social <span class="text-danger">*</span></label>
                <input type="text" name="nombre" id="nombreEmpresa" value="{{ old('nombre') }}"
                    class="form-control @error('nombre') is-invalid @enderror" required>
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Encargado / Contacto</label>
                <input type="text" name="encargado" value="{{ old('encargado') }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-medium py-3">Servicios contratados</div>
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="panel_digital" value="1" id="pd"
                            {{ old('panel_digital') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pd">Panel Digital</label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="panel_tradicional" value="1" id="pt"
                            {{ old('panel_tradicional') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pt">Panel Tradicional</label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="marketing_digital" value="1" id="md"
                            {{ old('marketing_digital') ? 'checked' : '' }}>
                        <label class="form-check-label" for="md">Marketing Digital</label>
                    </div>
                </div>
            </div>
            <label class="form-label">Otros servicios</label>
            <textarea name="otros_servicios" class="form-control" rows="2"
                placeholder="Describe otros servicios si aplica...">{{ old('otros_servicios') }}</textarea>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>Crear Empresa
        </button>
    </div>
</form>

</div>
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
        resultDiv.innerHTML = '<div class="alert alert-warning py-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i>El RUC debe tener exactamente 11 dígitos.</div>';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Buscando...';
    resultDiv.style.display = 'none';

    fetch('/sunat/ruc/' + ruc)
    .then(function (res) {
        if (!res.ok) throw new Error('No encontrado');
        return res.json();
    })
    .then(function (data) {
        if (!data.nombre) throw new Error('Sin datos');

        document.getElementById('nombreEmpresa').value = data.nombre;

        var estadoColor = data.estado === 'ACTIVO' ? 'success' : 'warning';
        var condicionColor = data.condicion === 'HABIDO' ? 'success' : 'danger';
        var dir = [data.direccion, data.distrito, data.provincia, data.departamento]
            .filter(function(v) { return v && v !== '-'; }).join(', ');

        resultDiv.style.display = 'block';
        resultDiv.innerHTML =
            '<div class="alert alert-success py-2 mb-0">' +
            '<div class="d-flex align-items-center gap-2 mb-1">' +
            '<i class="bi bi-building-check text-success fs-5"></i>' +
            '<strong>' + data.nombre + '</strong>' +
            '</div>' +
            '<div class="row g-1 small">' +
            '<div class="col-sm-6"><span class="text-muted">RUC:</span> ' + ruc + '</div>' +
            '<div class="col-sm-6"><span class="text-muted">Estado:</span> <span class="badge bg-' + estadoColor + '">' + (data.estado || '—') + '</span></div>' +
            '<div class="col-sm-6"><span class="text-muted">Condición:</span> <span class="badge bg-' + condicionColor + '">' + (data.condicion || '—') + '</span></div>' +
            (dir ? '<div class="col-12"><span class="text-muted">Dirección:</span> ' + dir + '</div>' : '') +
            '</div>' +
            '<div class="mt-2 text-success small"><i class="bi bi-check-circle me-1"></i>Nombre completado. Podés ajustarlo si es necesario.</div>' +
            '</div>';
    })
    .catch(function () {
        resultDiv.style.display = 'block';
        resultDiv.innerHTML =
            '<div class="alert alert-danger py-2 mb-0">' +
            '<i class="bi bi-x-circle me-1"></i>' +
            '<strong>RUC no encontrado.</strong> Completá los datos manualmente.' +
            '</div>';
    })
    .finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search me-1"></i>Buscar';
    });
}
</script>
@endpush
