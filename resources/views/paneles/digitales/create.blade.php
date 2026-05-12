@extends('layouts.app')

@section('title', 'Nuevo Panel Digital')
@section('subtitle', 'Registrar un nuevo panel digital')

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('paneles-digitales.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nuevo Panel Digital</div>
    </div>
</div>

<form action="{{ route('paneles-digitales.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header"><span><i class="bi bi-display"></i>Datos del panel</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control" placeholder="Ej: PD-001">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Medidas</label>
                    <input type="text" name="medidas" value="{{ old('medidas') }}" class="form-control" placeholder="Ej: 6x3m">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Resolución</label>
                    <input type="text" name="resolucion" value="{{ old('resolucion') }}" class="form-control" placeholder="Ej: 1920x1080">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Orientación</label>
                    <select name="orientacion" class="form-select">
                        <option value="">—</option>
                        <option value="horizontal" {{ old('orientacion') === 'horizontal' ? 'selected' : '' }}>Horizontal</option>
                        <option value="vertical" {{ old('orientacion') === 'vertical' ? 'selected' : '' }}>Vertical</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tandas</label>
                    <input type="number" name="tandas" value="{{ old('tandas') }}" class="form-control" min="1">
                </div>
                <div class="col-12">
                    <label class="form-label">Geolocalización</label>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <button type="button" class="btn btn-outline" onclick="obtenerUbicacion()" id="btnGeo">
                            <i class="bi bi-geo-alt-fill"></i>Obtener ubicación actual
                        </button>
                        <span id="geoStatus" style="font-size:12px;color:var(--text-light)">
                            @if(old('lat') && old('lng'))
                                <i class="bi bi-check-circle-fill" style="color:#10B981"></i> Lat: {{ old('lat') }}, Lng: {{ old('lng') }}
                            @else
                                Sin ubicación registrada
                            @endif
                        </span>
                    </div>
                    <input type="hidden" name="lat" id="latInput" value="{{ old('lat') }}">
                    <input type="hidden" name="lng" id="lngInput" value="{{ old('lng') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" accept="image/*" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('paneles-digitales.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i>Crear Panel</button>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
function obtenerUbicacion() {
    var btn = document.getElementById('btnGeo');
    var status = document.getElementById('geoStatus');
    if (!navigator.geolocation) {
        status.innerHTML = '<i class="bi bi-exclamation-triangle-fill" style="color:#F59E0B"></i> Geolocalización no soportada en este navegador.';
        return;
    }
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i>Obteniendo...';
    navigator.geolocation.getCurrentPosition(function(pos) {
        var lat = pos.coords.latitude.toFixed(7);
        var lng = pos.coords.longitude.toFixed(7);
        document.getElementById('latInput').value = lat;
        document.getElementById('lngInput').value = lng;
        status.innerHTML = '<i class="bi bi-check-circle-fill" style="color:#10B981"></i> Lat: ' + lat + ', Lng: ' + lng;
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i>Obtener ubicación actual';
    }, function() {
        status.innerHTML = '<i class="bi bi-x-circle-fill" style="color:var(--primary)"></i> No se pudo obtener la ubicación.';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i>Obtener ubicación actual';
    });
}
</script>
@endpush
