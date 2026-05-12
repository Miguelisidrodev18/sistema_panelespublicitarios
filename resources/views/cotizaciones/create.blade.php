@extends('layouts.app')

@section('title', 'Nueva Cotización')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">

<div class="d-flex align-items-center mb-3">
    <a href="{{ route('cotizaciones.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Nueva Cotización</h5>
    <span class="badge bg-secondary ms-2 fw-normal">{{ $numero }}</span>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('cotizaciones.store') }}" method="POST">
@csrf
<input type="hidden" name="numero" value="{{ old('numero', $numero) }}">

{{-- SECCIÓN 1: Datos del cliente --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="cot-section-title">
        <i class="bi bi-person-fill"></i> Datos del Cliente
    </div>
    <div class="p-4">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-medium">Empresa registrada en el sistema</label>
                <select name="empresa_id" id="empresa_id" class="form-select">
                    <option value="">— Cliente externo (no registrado) —</option>
                    @foreach($empresas as $emp)
                        <option value="{{ $emp->id }}"
                            data-nombre="{{ $emp->nombre }}"
                            data-encargado="{{ $emp->encargado ?? '' }}"
                            {{ old('empresa_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->nombre }}{{ $emp->encargado ? ' — '.$emp->encargado : '' }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Al seleccionar, se completan automáticamente los campos de abajo.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nombre del contacto</label>
                <input type="text" name="cliente_nombre" id="cliente_nombre"
                    value="{{ old('cliente_nombre') }}" class="form-control"
                    placeholder="Nombre completo">
            </div>
            <div class="col-md-6">
                <label class="form-label">Empresa / Negocio</label>
                <input type="text" name="cliente_empresa" id="cliente_empresa"
                    value="{{ old('cliente_empresa') }}" class="form-control"
                    placeholder="Razón social">
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="cliente_telefono"
                    value="{{ old('cliente_telefono') }}" class="form-control"
                    placeholder="999 999 999">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="cliente_email"
                    value="{{ old('cliente_email') }}" class="form-control"
                    placeholder="correo@ejemplo.com">
            </div>
        </div>
    </div>
</div>

{{-- SECCIÓN 2: Datos de la cotización --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="cot-section-title">
        <i class="bi bi-file-invoice-dollar"></i> Datos de la Cotización
    </div>
    <div class="p-4">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Tipo de servicio</label>
                <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato') }}"
                    class="form-control" list="tipos_cotizacion"
                    placeholder="Panel Digital, Tradicional...">
                <datalist id="tipos_cotizacion">
                    <option value="Panel Digital">
                    <option value="Panel Tradicional">
                    <option value="Marketing Digital">
                    <option value="Mixto">
                </datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label">Monto propuesto (S/.)</label>
                <input type="number" name="monto_propuesto"
                    value="{{ old('monto_propuesto', 0) }}"
                    class="form-control" step="1" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label">N° Cotización</label>
                <input type="text" class="form-control bg-light" value="{{ $numero }}" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha cotización</label>
                <input type="date" name="fecha_cotizacion"
                    value="{{ old('fecha_cotizacion', date('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Válida hasta</label>
                <input type="date" name="fecha_vencimiento"
                    value="{{ old('fecha_vencimiento') }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Notas / Descripción</label>
                <textarea name="notas" class="form-control" rows="3"
                    placeholder="Detalles del servicio, condiciones, propuesta...">{{ old('notas') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- SECCIÓN 3: Paneles de interés --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="cot-section-title">
        <i class="bi bi-geo-alt-fill"></i> Paneles de Interés
    </div>
    <div class="p-4">

        {{-- Digitales --}}
        <div class="cot-panel-group mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2" style="font-weight:600;font-size:13px">
                    <i class="bi bi-display" style="color:#2563EB;font-size:16px"></i>
                    Digital Outdoor
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary" id="cnt-digital">0</span>
                </div>
                <button type="button" class="cot-add-btn" onclick="addPanel('digital')">
                    <i class="bi bi-plus-lg"></i> Agregar panel
                </button>
            </div>
            <div id="cont-digital">
                <div class="cot-empty" id="empty-digital">
                    <i class="bi bi-display me-1 opacity-50"></i>Sin paneles digitales — hacé clic en "Agregar panel"
                </div>
            </div>
        </div>

        {{-- Tradicionales --}}
        <div class="cot-panel-group">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2" style="font-weight:600;font-size:13px">
                    <i class="bi bi-signpost-2" style="color:#EA580C;font-size:16px"></i>
                    Tradicional Outdoor
                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning" id="cnt-tradicional">0</span>
                </div>
                <button type="button" class="cot-add-btn" onclick="addPanel('tradicional')">
                    <i class="bi bi-plus-lg"></i> Agregar panel
                </button>
            </div>
            <div id="cont-tradicional">
                <div class="cot-empty" id="empty-tradicional">
                    <i class="bi bi-signpost-2 me-1 opacity-50"></i>Sin paneles tradicionales — hacé clic en "Agregar panel"
                </div>
            </div>
        </div>

        {{-- Servicios adicionales --}}
        <div class="cot-panel-group mt-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2" style="font-weight:600;font-size:13px">
                    <i class="bi bi-box-seam" style="color:#7C3AED;font-size:16px"></i>
                    Servicios Adicionales
                    <span class="badge rounded-pill" style="background:#EDE9FE;color:#7C3AED" id="cnt-servicio">0</span>
                </div>
                <button type="button" class="cot-add-btn" style="border-color:#7C3AED;color:#7C3AED" onclick="addServicio()">
                    <i class="bi bi-plus-lg"></i> Agregar servicio
                </button>
            </div>
            <div id="cont-servicio">
                <div class="cot-empty" id="empty-servicio">
                    <i class="bi bi-box-seam me-1 opacity-50"></i>Sin servicios — hacé clic en "Agregar servicio"
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal foto panel --}}
<div id="modalFotoPanel" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;max-width:520px;width:90%;padding:20px;position:relative">
        <button type="button" onclick="cerrarFotoPanel()" style="position:absolute;top:10px;right:14px;background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
        <div class="fw-700 mb-3" id="fotoNombrePanel">Panel</div>
        <img id="fotoPanelImg" src="" style="width:100%;border-radius:8px;object-fit:cover;max-height:320px" alt="Foto del panel">
        <div id="fotoPanelNoImg" style="display:none;padding:30px;text-align:center;color:#9CA3AF;border:1px dashed #D1D5DB;border-radius:8px">
            <i class="bi bi-image" style="font-size:32px"></i><div>Sin foto disponible</div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 justify-content-end mb-4">
    <a href="{{ route('cotizaciones.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-lg me-1"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-danger px-4">
        <i class="bi bi-check-lg me-1"></i>Guardar Cotización
    </button>
</div>

</form>
</div>
</div>
@endsection

@push('styles')
<style>
.cot-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #374151;
    padding: 10px 20px;
    background: #F8FAFC;
    border-bottom: 1px solid #E2E8F0;
}
.cot-section-title i { color: var(--primary); font-size: 14px; }

.cot-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 14px;
    border: 1px solid var(--primary);
    color: var(--primary);
    background: transparent;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s;
}
.cot-add-btn:hover { background: #FEF2F2; }

.cot-empty {
    padding: 12px;
    text-align: center;
    font-size: 13px;
    color: #9CA3AF;
    border: 1px dashed #D1D5DB;
    border-radius: 8px;
}

.cot-panel-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    margin-bottom: 6px;
}
.cot-panel-row select  { flex: 2; min-width: 0; }
.cot-panel-row .f-cod  { width: 90px; flex-shrink: 0; }
.cot-panel-row .f-mes  { width: 80px; flex-shrink: 0; }
.cot-panel-row .f-pre  { width: 110px; flex-shrink: 0; }
</style>
@endpush

@push('scripts')
<script>
var paneles = {
    digital:     @json($paneles_digitales->map(fn($p) => ['id' => $p->id, 'codigo' => $p->codigo, 'nombre' => $p->nombre])),
    tradicional: @json($paneles_tradicionales->map(fn($p) => ['id' => $p->id, 'codigo' => $p->codigo, 'nombre' => $p->nombre]))
};
var serviciosDisp = @json($servicios->map(fn($s) => ['id' => $s->id, 'nombre' => $s->nombre, 'monto' => $s->monto]));
var counters = { digital: 0, tradicional: 0, servicio: 0 };

function updateCount(tipo) {
    var n = document.getElementById('cont-' + tipo).querySelectorAll('.cot-panel-row').length;
    document.getElementById('cnt-' + tipo).textContent = n;
}

function addPanel(tipo) {
    var cont  = document.getElementById('cont-' + tipo);
    var empty = document.getElementById('empty-' + tipo);
    if (empty) empty.style.display = 'none';

    var idx  = counters[tipo]++;
    var opts = '<option value="">Seleccionar panel...</option>' +
        paneles[tipo].map(function(p) {
            return '<option value="' + p.id + '" data-codigo="' + (p.codigo||'') + '">' +
                   (p.codigo ? p.codigo + ' — ' : '') + p.nombre + '</option>';
        }).join('');

    var row = document.createElement('div');
    row.className = 'cot-panel-row';
    row.id = 'row-' + tipo + '-' + idx;
    row.innerHTML =
        '<select name="elemento_panel_id[]" class="form-select form-select-sm" ' +
            'onchange="onSelect(this,\'' + tipo + '\',' + idx + ')">' + opts + '</select>' +
        '<input type="hidden" name="elemento_tipo[]" value="' + tipo + '">' +
        '<input type="text"   name="elemento_codigo[]" class="form-control form-control-sm f-cod" placeholder="Código" readonly>' +
        '<input type="number" name="elemento_tiempo[]" class="form-control form-control-sm f-mes" placeholder="Meses" min="1">' +
        '<input type="number" name="elemento_precio[]" class="form-control form-control-sm f-pre" placeholder="S/. Precio" min="0" step="1">' +
        '<button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0" title="Ver foto" ' +
            'onclick="verFotoPanel(\'' + tipo + '\',' + idx + ')" id="btnFoto-' + tipo + '-' + idx + '" style="display:none">' +
            '<i class="bi bi-image"></i></button>' +
        '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" ' +
            'onclick="removePanel(\'' + tipo + '\',' + idx + ')">' +
            '<i class="bi bi-trash"></i></button>';

    cont.appendChild(row);
    updateCount(tipo);
}

function onSelect(sel, tipo, idx) {
    var opt = sel.options[sel.selectedIndex];
    var row = document.getElementById('row-' + tipo + '-' + idx);
    if (row) {
        row.querySelector('input[name="elemento_codigo[]"]').value = opt.dataset.codigo || '';
        var btnFoto = document.getElementById('btnFoto-' + tipo + '-' + idx);
        if (btnFoto) btnFoto.style.display = sel.value ? '' : 'none';
    }
}

function verFotoPanel(tipo, idx) {
    var row = document.getElementById('row-' + tipo + '-' + idx);
    var panelId = row ? row.querySelector('select').value : null;
    if (!panelId) return;
    fetch('/panel-foto/' + tipo + '/' + panelId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('fotoNombrePanel').textContent = data.nombre || 'Panel';
            var img = document.getElementById('fotoPanelImg');
            var noImg = document.getElementById('fotoPanelNoImg');
            if (data.foto) {
                img.src = data.foto;
                img.style.display = '';
                noImg.style.display = 'none';
            } else {
                img.style.display = 'none';
                noImg.style.display = '';
            }
            document.getElementById('modalFotoPanel').style.display = 'flex';
        });
}

function cerrarFotoPanel() {
    document.getElementById('modalFotoPanel').style.display = 'none';
}

function removePanel(tipo, idx) {
    var row = document.getElementById('row-' + tipo + '-' + idx);
    if (row) row.remove();
    var cont = document.getElementById('cont-' + tipo);
    if (!cont.querySelector('.cot-panel-row')) {
        document.getElementById('empty-' + tipo).style.display = '';
    }
    updateCount(tipo);
}

function addServicio() {
    var cont  = document.getElementById('cont-servicio');
    var empty = document.getElementById('empty-servicio');
    if (empty) empty.style.display = 'none';

    var idx  = counters['servicio']++;
    var opts = '<option value="">Seleccionar servicio...</option>' +
        serviciosDisp.map(function(s) {
            return '<option value="' + s.id + '" data-monto="' + s.monto + '">' + s.nombre + ' (S/. ' + parseFloat(s.monto).toFixed(2) + ')</option>';
        }).join('');

    var row = document.createElement('div');
    row.className = 'cot-panel-row';
    row.id = 'row-servicio-' + idx;
    row.innerHTML =
        '<select name="srv_id[]" class="form-select form-select-sm" style="flex:3" ' +
            'onchange="onSelectSrv(this,' + idx + ')">' + opts + '</select>' +
        '<input type="number" name="srv_precio[]" class="form-control form-control-sm f-pre" placeholder="S/. Precio" min="0" step="0.01">' +
        '<input type="text"   name="srv_obs[]"   class="form-control form-control-sm" placeholder="Observaciones" style="flex:2">' +
        '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" ' +
            'onclick="removeServicio(' + idx + ')"><i class="bi bi-trash"></i></button>';

    cont.appendChild(row);
    updateCount('servicio');
}

function onSelectSrv(sel, idx) {
    var opt = sel.options[sel.selectedIndex];
    var row = document.getElementById('row-servicio-' + idx);
    if (row && opt.dataset.monto) {
        row.querySelector('input[name="srv_precio[]"]').value = parseFloat(opt.dataset.monto).toFixed(2);
    }
}

function removeServicio(idx) {
    var row = document.getElementById('row-servicio-' + idx);
    if (row) row.remove();
    var cont = document.getElementById('cont-servicio');
    if (!cont.querySelector('.cot-panel-row')) {
        document.getElementById('empty-servicio').style.display = '';
    }
    updateCount('servicio');
}

// Auto-rellenar empresa
document.getElementById('empresa_id').addEventListener('change', function () {
    var opt = this.options[this.selectedIndex];
    document.getElementById('cliente_nombre').value  = opt.dataset.encargado || '';
    document.getElementById('cliente_empresa').value = opt.dataset.nombre    || '';
});
</script>
@endpush
