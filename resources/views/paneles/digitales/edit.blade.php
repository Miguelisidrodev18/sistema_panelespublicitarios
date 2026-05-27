@extends('layouts.app')

@section('title', 'Editar Panel Digital')
@section('subtitle', $panelDigital->nombre)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.leaflet-container { font-family: inherit; }
.mapa-hint { margin-top:6px; font-size:12px; color:var(--text-light); display:flex; align-items:center; gap:5px; }
</style>
@endpush

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
                    <input type="text" name="direccion" id="inputDireccion" value="{{ old('direccion', $panelDigital->direccion) }}" class="form-control">
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
                <div class="col-md-4">
                    <label class="form-label">Costo de producción (S/.)</label>
                    <input type="number" name="costo_produccion"
                        value="{{ old('costo_produccion', $panelDigital->costo_produccion) }}"
                        class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Descripción del costo</label>
                    <input type="text" name="desc_costo"
                        value="{{ old('desc_costo', $panelDigital->desc_costo ?? 'Instalación y puesta en marcha') }}"
                        class="form-control" placeholder="Ej: Instalación y puesta en marcha" maxlength="255">
                    <div class="form-hint">Describe qué incluye el costo de producción.</div>
                </div>

                {{-- Mapa de ubicación --}}
                <div class="col-12">
                    <label class="form-label"><i class="bi bi-map" style="color:#2563EB;margin-right:5px"></i>Ubicación en el mapa</label>
                    <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;flex-wrap:wrap">
                        <button type="button" id="btnGeoActual" onclick="usarUbicacionActual()" class="btn btn-sm btn-secondary">
                            <i class="bi bi-crosshair"></i> Mi ubicación
                        </button>
                        <input type="text" id="buscarDireccion" class="form-control" style="max-width:320px"
                               placeholder="Buscar dirección en el mapa..."
                               onkeydown="if(event.key==='Enter'){event.preventDefault();buscarEnMapa()}">
                        <button type="button" onclick="buscarEnMapa()" class="btn btn-sm btn-primary btn-icon" title="Buscar">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div id="mapaPanel" style="height:320px;border-radius:10px;border:1.5px solid var(--border);overflow:hidden"></div>
                    <div id="coordsInfo" class="mapa-hint">
                        @if(old('lat', $panelDigital->lat) && old('lng', $panelDigital->lng))
                            <i class="bi bi-check-circle-fill" style="color:#10B981"></i>
                            Lat: <strong>{{ old('lat', $panelDigital->lat) }}</strong>,
                            Lng: <strong>{{ old('lng', $panelDigital->lng) }}</strong>
                        @else
                            <i class="bi bi-info-circle"></i> Haz clic en el mapa o arrastra el marcador para fijar la ubicación.
                        @endif
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    const initLat = parseFloat('{{ old("lat", $panelDigital->lat) }}') || null;
    const initLng = parseFloat('{{ old("lng", $panelDigital->lng) }}') || null;
    const center  = (initLat && initLng) ? [initLat, initLng] : [-13.5, -72.5];
    const zoom    = (initLat && initLng) ? 15 : 7;

    const map = L.map('mapaPanel').setView(center, zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>', maxZoom: 19
    }).addTo(map);

    let marker = null;
    if (initLat && initLng) {
        marker = L.marker([initLat, initLng], {draggable: true}).addTo(map);
        marker.on('dragend', e => setCoords(e.target.getLatLng().lat, e.target.getLatLng().lng));
    }

    map.on('click', e => placeMarker(e.latlng.lat, e.latlng.lng));

    function placeMarker(lat, lng) {
        if (marker) { marker.setLatLng([lat, lng]); }
        else {
            marker = L.marker([lat, lng], {draggable: true}).addTo(map);
            marker.on('dragend', e => setCoords(e.target.getLatLng().lat, e.target.getLatLng().lng));
        }
        setCoords(lat, lng);
    }

    function setCoords(lat, lng) {
        document.getElementById('latInput').value = lat.toFixed(7);
        document.getElementById('lngInput').value = lng.toFixed(7);
        document.getElementById('coordsInfo').innerHTML =
            '<i class="bi bi-check-circle-fill" style="color:#10B981"></i> ' +
            'Lat: <strong>' + lat.toFixed(6) + '</strong>, Lng: <strong>' + lng.toFixed(6) + '</strong>';
    }

    window.usarUbicacionActual = function() {
        const btn = document.getElementById('btnGeoActual');
        if (!navigator.geolocation) { alert('Geolocalización no disponible en este navegador.'); return; }
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Obteniendo...';
        navigator.geolocation.getCurrentPosition(pos => {
            placeMarker(pos.coords.latitude, pos.coords.longitude);
            map.setView([pos.coords.latitude, pos.coords.longitude], 17);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-crosshair"></i> Mi ubicación';
        }, () => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-crosshair"></i> Mi ubicación';
            alert('No se pudo obtener la ubicación.');
        });
    };

    window.buscarEnMapa = function() {
        const q = document.getElementById('buscarDireccion').value.trim();
        if (!q) return;
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=1', {
            headers: {'Accept-Language': 'es'}
        })
        .then(r => r.json())
        .then(data => {
            if (!data.length) { alert('Dirección no encontrada. Intenta con un texto más específico.'); return; }
            const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
            placeMarker(lat, lng);
            map.setView([lat, lng], 17);
        })
        .catch(() => alert('Error al buscar la dirección.'));
    };
})();
</script>
@endpush
