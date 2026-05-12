@extends('layouts.app')

@section('title', 'Editar Panel Digital')
@section('subtitle', $panelDigital->nombre)

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('paneles-digitales.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar: {{ $panelDigital->nombre }}</div>
    </div>
</div>

<form action="{{ route('paneles-digitales.update', $panelDigital) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header"><span><i class="bi bi-display"></i>Datos del panel</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $panelDigital->codigo) }}" class="form-control">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nombre <span class="req">*</span></label>
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
                        <option value="">—</option>
                        @foreach(['horizontal','vertical'] as $o)
                        <option value="{{ $o }}" {{ old('orientacion', $panelDigital->orientacion) === $o ? 'selected' : '' }}>{{ ucfirst($o) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tandas</label>
                    <input type="number" name="tandas" value="{{ old('tandas', $panelDigital->tandas) }}" class="form-control" min="1">
                </div>
                <div class="col-12">
                    <label class="form-label">Geolocalización</label>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <button type="button" class="btn btn-outline" onclick="obtenerUbicacion()" id="btnGeo">
                            <i class="bi bi-geo-alt-fill"></i>Obtener ubicación actual
                        </button>
                        <span id="geoStatus" style="font-size:12px;color:var(--text-light)">
                            @if(old('lat', $panelDigital->lat) && old('lng', $panelDigital->lng))
                                <i class="bi bi-check-circle-fill" style="color:#10B981"></i> Lat: {{ old('lat', $panelDigital->lat) }}, Lng: {{ old('lng', $panelDigital->lng) }}
                            @else
                                Sin ubicación registrada
                            @endif
                        </span>
                    </div>
                    <input type="hidden" name="lat" id="latInput" value="{{ old('lat', $panelDigital->lat) }}">
                    <input type="hidden" name="lng" id="lngInput" value="{{ old('lng', $panelDigital->lng) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" accept="image/*" class="form-control">
                    @if($panelDigital->foto)
                        <div style="margin-top:8px"><img src="{{ Storage::url($panelDigital->foto) }}" height="80" style="border-radius:8px;object-fit:cover"></div>
                    @endif
                </div>
                <div class="col-auto" style="display:flex;align-items:flex-end">
                    <label class="toggle-switch">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $panelDigital->activo) ? 'checked' : '' }}>
                        <span>Panel activo</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="action-bar">
        <a href="{{ route('paneles-digitales.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
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
