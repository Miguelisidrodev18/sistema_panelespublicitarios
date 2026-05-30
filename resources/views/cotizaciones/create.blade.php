@extends('layouts.app')

@section('title', 'Nueva Cotización')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-9">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('cotizaciones.index') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Nueva Cotización <span class="badge badge-purple" style="font-size:12px;margin-left:8px">{{ $numero }}</span></div>
    </div>
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
<div class="card mb-3">
    <div class="card-header ch-blue">
        <span><i class="bi bi-person"></i>Datos del Cliente</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-medium">Empresa del sistema</label>
                <input type="hidden" name="empresa_id" id="empresa_id" value="{{ old('empresa_id') }}">
                <div style="position:relative">
                    <div id="create-emp-chip" style="display:none;margin-bottom:6px;padding:7px 12px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;align-items:center;gap:10px">
                        <i class="bi bi-building" style="color:#059669;font-size:15px;flex-shrink:0"></i>
                        <span id="create-emp-chip-name" style="flex:1;font-size:13px;font-weight:600;color:#065F46"></span>
                        <span id="create-emp-chip-ruc" style="font-size:11px;color:#64748B"></span>
                        <button type="button" onclick="empClear('create')"
                                style="background:none;border:none;cursor:pointer;color:#94A3B8;font-size:16px;line-height:1;padding:0">&times;</button>
                    </div>
                    <input type="text" id="create-emp-search" autocomplete="off"
                           class="form-control"
                           placeholder="Buscar empresa por nombre, RUC o encargado..."
                           oninput="empSearch(this.value,'create')"
                           onfocus="empSearch(this.value,'create')">
                    <div id="create-emp-drop" class="emp-drop" style="display:none"></div>
                </div>
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
<div class="card mb-3">
    <div class="card-header ch-purple">
        <span><i class="bi bi-receipt"></i>Datos de la Cotización</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
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
            <div class="col-md-6">
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
<div class="card mb-3">
    <div class="card-header ch-amber">
        <span><i class="bi bi-geo-alt-fill"></i>Paneles de Interés</span>
    </div>
    <div class="card-body">

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

{{-- Resumen de totales con IGV --}}
<div class="card mb-3" id="resumen-totales" style="display:none">
    <div class="card-header ch-green">
        <span><i class="bi bi-calculator"></i>Resumen de Totales</span>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-end">
            <table style="font-size:13px;min-width:260px">
                <tr>
                    <td class="pe-4 text-muted">Subtotal neto</td>
                    <td class="fw-600 text-end">S/. <span id="res-subtotal">0.00</span></td>
                </tr>
                <tr>
                    <td class="pe-4 text-muted">IGV (18%)</td>
                    <td class="fw-600 text-end">S/. <span id="res-igv">0.00</span></td>
                </tr>
                <tr style="border-top:2px solid #E2E8F0">
                    <td class="pe-4 fw-700 pt-2" style="font-size:14px">TOTAL CON IGV</td>
                    <td class="fw-800 text-end pt-2" style="font-size:14px;color:#059669">S/. <span id="res-total">0.00</span></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="d-flex gap-2 justify-content-end mb-4">
    <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary">
        <i class="bi bi-x-lg me-1"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-primary px-4">
        <i class="bi bi-check-lg me-1"></i>Guardar Cotización
    </button>
</div>

</form>
</div>
</div>
@endsection

@push('styles')
<style>
.emp-drop {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: #fff; border: 1px solid #E2E8F0; border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 999;
    max-height: 260px; overflow-y: auto;
}
.emp-drop-item {
    padding: 10px 14px; cursor: pointer;
    border-bottom: 1px solid #F8FAFC; transition: background .12s;
}
.emp-drop-item:last-child { border-bottom: none; }
.emp-drop-item:hover { background: #F0FDF4; }
.cot-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--primary-dark);
    padding: 12px 20px;
    background: linear-gradient(90deg, rgba(230,57,70,0.06), rgba(255,255,255,0.7));
    border-left: 4px solid var(--primary);
    border-bottom: 1px solid rgba(226,232,240,.6);
}
.cot-section-title i { color: var(--primary); font-size: 14px; }

.cot-add-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border: 1.5px solid var(--primary);
    color: var(--primary); background: transparent;
    border-radius: var(--radius-sm); font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all .2s ease;
}
.cot-add-btn:hover {
    background: var(--primary-lighter);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(230,57,70,.12);
}

.cot-empty {
    padding: 16px; text-align: center; font-size: 13px;
    color: var(--text-light); border: 1px dashed var(--border); border-radius: var(--radius-md);
    background: rgba(255,255,255,.3);
}

.cot-panel-row {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 10px 14px; background: rgba(255,255,255,.6);
    border: 1px solid rgba(226,232,240,.8); border-radius: var(--radius-md); margin-bottom: 8px;
    transition: all .2s ease;
    backdrop-filter: blur(4px);
}
.cot-panel-row:hover {
    border-color: rgba(230,57,70,.25);
    box-shadow: var(--shadow-sm);
}
.cot-panel-row select  { flex: 2; min-width: 0; }
.cot-panel-row .f-cod  { width: 90px; flex-shrink: 0; }
.cot-panel-row .f-mes  { width: 80px; flex-shrink: 0; }
.cot-panel-row .f-pre  { width: 110px; flex-shrink: 0; }
</style>
@endpush

@push('scripts')
<script>
@php
$_cPanDigital = $paneles_digitales->map(function($p) {
    return ['id' => $p->id, 'codigo' => $p->codigo, 'nombre' => $p->nombre, 'costo' => $p->costo_produccion ?? 0, 'desc' => $p->desc_costo ?? 'Instalación y puesta en marcha'];
})->values();
$_cPanTradicional = $paneles_tradicionales->map(function($p) {
    return ['id' => $p->id, 'codigo' => $p->codigo, 'nombre' => $p->nombre, 'costo' => $p->costo_produccion ?? 0, 'desc' => $p->desc_costo ?? 'Producción de lona e instalación'];
})->values();
$_cServicios = $servicios->map(function($s) {
    return ['id' => $s->id, 'nombre' => $s->nombre, 'monto' => $s->monto];
})->values();
@endphp
var paneles = {
    digital:     @json($_cPanDigital),
    tradicional: @json($_cPanTradicional)
};
var serviciosDisp = @json($_cServicios);
var counters = { digital: 0, tradicional: 0, servicio: 0 };
var IGV = 0.18;

function updateCount(tipo) {
    var n = document.getElementById('cont-' + tipo).querySelectorAll('.cot-panel-row').length;
    document.getElementById('cnt-' + tipo).textContent = n;
}

function recalcularTotales() {
    var subtotal = 0;
    document.querySelectorAll('input[name="elemento_precio[]"]').forEach(function(inp) {
        subtotal += parseFloat(inp.value) || 0;
    });
    document.querySelectorAll('input[name="elemento_costo[]"]').forEach(function(inp) {
        subtotal += parseFloat(inp.value) || 0;
    });
    document.querySelectorAll('input[name="srv_precio[]"]').forEach(function(inp) {
        subtotal += parseFloat(inp.value) || 0;
    });
    var igv   = subtotal * IGV;
    var total = subtotal + igv;
    var resumen = document.getElementById('resumen-totales');
    if (subtotal > 0) {
        resumen.style.display = '';
        document.getElementById('res-subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('res-igv').textContent      = igv.toFixed(2);
        document.getElementById('res-total').textContent    = total.toFixed(2);
    } else {
        resumen.style.display = 'none';
    }
}

function addPanel(tipo) {
    var cont  = document.getElementById('cont-' + tipo);
    var empty = document.getElementById('empty-' + tipo);
    if (empty) empty.style.display = 'none';

    var idx  = counters[tipo]++;
    var opts = '<option value="">Seleccionar panel...</option>' +
        paneles[tipo].map(function(p) {
            return '<option value="' + p.id + '" data-codigo="' + (p.codigo||'') + '" data-costo="' + (p.costo||0) + '" data-desc="' + (p.desc||'') + '">' +
                   (p.codigo ? p.codigo + ' — ' : '') + p.nombre + '</option>';
        }).join('');

    var descDefault = tipo === 'tradicional' ? 'Producción de lona e instalación' : 'Instalación y puesta en marcha';

    var row = document.createElement('div');
    row.className = 'cot-panel-row flex-wrap';
    row.id = 'row-' + tipo + '-' + idx;
    row.innerHTML =
        '<select name="elemento_panel_id[]" class="form-select form-select-sm" style="flex:3;min-width:180px" ' +
            'onchange="onSelect(this,\'' + tipo + '\',' + idx + ')">' + opts + '</select>' +
        '<input type="hidden" name="elemento_tipo[]" value="' + tipo + '">' +
        '<input type="text" name="elemento_codigo[]" class="form-control form-control-sm f-cod" placeholder="Código" readonly>' +
        '<input type="number" name="elemento_tiempo[]" class="form-control form-control-sm f-mes" placeholder="Meses" min="1" oninput="recalcularTotales()">' +
        '<input type="number" name="elemento_precio[]" class="form-control form-control-sm f-pre" placeholder="S/. Precio" min="0" step="0.01" oninput="recalcularTotales()">' +
        '<input type="number" name="elemento_costo[]" class="form-control form-control-sm f-pre" placeholder="S/. Costo prod." min="0" step="0.01" id="costo-' + tipo + '-' + idx + '" oninput="recalcularTotales()">' +
        '<input type="text" name="elemento_desc_costo[]" class="form-control form-control-sm" placeholder="Desc. costo (ej: Prod. lona e instalación)" style="flex:2;min-width:160px" value="' + descDefault + '">' +
        '<button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0" title="Ver foto" ' +
            'onclick="verFotoPanel(\'' + tipo + '\',' + idx + ')" id="btnFoto-' + tipo + '-' + idx + '" style="display:none">' +
            '<i class="bi bi-image"></i></button>' +
        '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" ' +
            'onclick="removePanel(\'' + tipo + '\',' + idx + ')"><i class="bi bi-trash"></i></button>';

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
        var costoInp = document.getElementById('costo-' + tipo + '-' + idx);
        if (costoInp && opt.dataset.costo) costoInp.value = parseFloat(opt.dataset.costo||0).toFixed(2);
        var descInp = row.querySelector('input[name="elemento_desc_costo[]"]');
        if (descInp && opt.dataset.desc) descInp.value = opt.dataset.desc;
        recalcularTotales();
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
            if (data.foto) { img.src = data.foto; img.style.display = ''; noImg.style.display = 'none'; }
            else { img.style.display = 'none'; noImg.style.display = ''; }
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
    if (!cont.querySelector('.cot-panel-row')) document.getElementById('empty-' + tipo).style.display = '';
    updateCount(tipo);
    recalcularTotales();
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
    row.className = 'cot-panel-row flex-wrap';
    row.id = 'row-servicio-' + idx;
    row.innerHTML =
        '<select name="srv_id[]" class="form-select form-select-sm" style="flex:3;min-width:160px" ' +
            'onchange="onSelectSrv(this,' + idx + ')">' + opts + '</select>' +
        '<select name="srv_subtipo[]" class="form-select form-select-sm" style="width:100px;flex-shrink:0">' +
            '<option value="">Tipo</option>' +
            '<option value="led">LED</option>' +
            '<option value="banner">BANNER</option>' +
            '<option value="general">GENERAL</option>' +
        '</select>' +
        '<input type="number" name="srv_precio[]" class="form-control form-control-sm f-pre" placeholder="S/. Precio" min="0" step="0.01" oninput="recalcularTotales()">' +
        '<input type="text" name="srv_obs[]" class="form-control form-control-sm" placeholder="Observaciones" style="flex:2;min-width:120px">' +
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
        recalcularTotales();
    }
}

function removeServicio(idx) {
    var row = document.getElementById('row-servicio-' + idx);
    if (row) row.remove();
    var cont = document.getElementById('cont-servicio');
    if (!cont.querySelector('.cot-panel-row')) document.getElementById('empty-servicio').style.display = '';
    updateCount('servicio');
    recalcularTotales();
}

// ── Búsqueda dinámica de empresa ─────────────────────────────
@php
$_cEmpresas = $empresas->map(fn($e) => [
    'id'       => $e->id,
    'nombre'   => $e->nombre,
    'ruc'      => $e->ruc ?? '',
    'encargado'=> $e->encargado ?? '',
    'celular'  => $e->celular ?? '',
    'correo'   => $e->correo ?? '',
])->values();
@endphp
var mEmpresas = @json($_cEmpresas);

function empSearch(q, ctx) {
    var drop  = document.getElementById(ctx+'-emp-drop');
    var lower = q.toLowerCase().trim();
    var list  = lower
        ? mEmpresas.filter(function(e){
            return e.nombre.toLowerCase().includes(lower)
                || e.ruc.toLowerCase().includes(lower)
                || e.encargado.toLowerCase().includes(lower);
          })
        : mEmpresas.slice(0, 12);

    if (list.length === 0) {
        drop.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:#94A3B8">Sin empresas encontradas</div>';
    } else {
        drop.innerHTML = list.map(function(e){
            var sub = [e.ruc ? 'RUC: '+e.ruc : '', e.encargado].filter(Boolean).join(' · ');
            return '<div class="emp-drop-item" onclick="empSelect('+e.id+',\''+ctx+'\')">'+
                '<div style="font-weight:600;font-size:13px;color:#0F172A">'+esc2(e.nombre)+'</div>'+
                (sub ? '<div style="font-size:11px;color:#94A3B8;margin-top:1px">'+esc2(sub)+'</div>' : '')+
                '</div>';
        }).join('');
    }
    drop.style.display = 'block';
}

function empSelect(id, ctx) {
    var e = mEmpresas.find(function(x){ return x.id === id; });
    if (!e) return;

    document.getElementById('empresa_id').value = e.id;
    document.getElementById(ctx+'-emp-search').value = '';
    document.getElementById(ctx+'-emp-drop').style.display = 'none';

    var chip = document.getElementById(ctx+'-emp-chip');
    chip.style.display = 'flex';
    document.getElementById(ctx+'-emp-chip-name').textContent = e.nombre;
    document.getElementById(ctx+'-emp-chip-ruc').textContent  = e.ruc ? 'RUC: '+e.ruc : '';

    var n = document.getElementById('cliente_nombre');
    var c = document.getElementById('cliente_empresa');
    var t = document.querySelector('[name="cliente_telefono"]');
    var m = document.querySelector('[name="cliente_email"]');
    if (n && !n.value) n.value = e.encargado || '';
    if (c && !c.value) c.value = e.nombre    || '';
    if (t && !t.value) t.value = e.celular   || '';
    if (m && !m.value) m.value = e.correo    || '';
}

function empClear(ctx) {
    document.getElementById('empresa_id').value = '';
    document.getElementById(ctx+'-emp-chip').style.display = 'none';
    document.getElementById(ctx+'-emp-search').value = '';
}

function esc2(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

document.addEventListener('click', function(e) {
    var drop  = document.getElementById('create-emp-drop');
    var input = document.getElementById('create-emp-search');
    if (drop && input && !input.contains(e.target) && !drop.contains(e.target))
        drop.style.display = 'none';
});

// Pre-seleccionar si viene con old('empresa_id')
@if(old('empresa_id'))
(function(){
    var id = {{ old('empresa_id') }};
    var e = mEmpresas.find(function(x){ return x.id === id; });
    if (e) {
        document.getElementById('create-emp-chip').style.display = 'flex';
        document.getElementById('create-emp-chip-name').textContent = e.nombre;
        document.getElementById('create-emp-chip-ruc').textContent  = e.ruc ? 'RUC: '+e.ruc : '';
    }
})();
@endif
</script>
@endpush
