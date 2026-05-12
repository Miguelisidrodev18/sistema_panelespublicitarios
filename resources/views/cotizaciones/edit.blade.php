@extends('layouts.app')

@section('title', 'Editar Cotización')
@section('subtitle', $cotizacion->numero)

@section('content')
<div class="form-card">

<div class="page-header">
    <div class="page-header-left">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
        <div class="page-title">Editar Cotización {{ $cotizacion->numero }}</div>
    </div>
</div>

@if($errors->any())
<div class="card card-accent" style="border-color:#FCA5A5;margin-bottom:16px">
    <div class="card-body" style="background:#FEF2F2;color:var(--primary);font-size:13px">
        <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

<form action="{{ route('cotizaciones.update', $cotizacion) }}" method="POST">
    @csrf @method('PUT')

    <div class="card">
        <div class="card-header"><span><i class="bi bi-person"></i>Datos del cliente</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre del cliente <span class="req">*</span></label>
                    <input type="text" name="cliente_nombre" value="{{ old('cliente_nombre', $cotizacion->cliente_nombre) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        @foreach(['pendiente'=>'Pendiente','aprobada'=>'Aprobada','rechazada'=>'Rechazada','convertida'=>'Convertida'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('estado', $cotizacion->estado) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Empresa del cliente</label>
                    <input type="text" name="cliente_empresa" value="{{ old('cliente_empresa', $cotizacion->cliente_empresa) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="cliente_telefono" value="{{ old('cliente_telefono', $cotizacion->cliente_telefono) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="cliente_email" value="{{ old('cliente_email', $cotizacion->cliente_email) }}" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span><i class="bi bi-file-invoice-dollar"></i>Detalles de la propuesta</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo de servicio</label>
                    <input type="text" name="tipo_contrato" value="{{ old('tipo_contrato', $cotizacion->tipo_contrato) }}" class="form-control" list="tipos_contrato">
                    <datalist id="tipos_contrato"><option value="Panel Digital"><option value="Panel Tradicional"><option value="Marketing Digital"><option value="Mixto"></datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Monto propuesto (S/.)</label>
                    <input type="number" name="monto_propuesto" value="{{ old('monto_propuesto', $cotizacion->monto_propuesto) }}" class="form-control" step="1" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha cotización</label>
                    <input type="date" name="fecha_cotizacion" value="{{ old('fecha_cotizacion', $cotizacion->fecha_cotizacion?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vence el</label>
                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $cotizacion->fecha_vencimiento?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Notas / Descripción</label>
                    <textarea name="notas" class="form-control" rows="3">{{ old('notas', $cotizacion->notas) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- PANELES DE INTERÉS --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="cot-section-title"><i class="bi bi-geo-alt-fill"></i>Paneles de Interés</div>
        <div class="p-4">
            {{-- Digitales --}}
            <div class="cot-panel-group mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2" style="font-weight:600;font-size:13px">
                        <i class="bi bi-display" style="color:#2563EB;font-size:16px"></i> Digital Outdoor
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary" id="cnt-digital">0</span>
                    </div>
                    <button type="button" class="cot-add-btn" onclick="addPanel('digital')"><i class="bi bi-plus-lg"></i> Agregar panel</button>
                </div>
                <div id="cont-digital">
                    @foreach($cotizacion->elementos->where('tipo_elemento','digital') as $el)
                    <div class="cot-panel-row" id="row-digital-pre-{{ $el->id }}">
                        <select name="elemento_panel_id[]" class="form-select form-select-sm">
                            <option value="">Seleccionar panel...</option>
                            @foreach($paneles_digitales as $pd)
                            <option value="{{ $pd->id }}" data-codigo="{{ $pd->codigo }}" {{ $el->panel_id == $pd->id ? 'selected' : '' }}>{{ $pd->codigo }} — {{ $pd->nombre }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="elemento_tipo[]" value="digital">
                        <input type="text" name="elemento_codigo[]" class="form-control form-control-sm f-cod" value="{{ $el->codigo }}" readonly>
                        <input type="number" name="elemento_tiempo[]" class="form-control form-control-sm f-mes" value="{{ $el->tiempo_contrato }}" placeholder="Meses" min="1">
                        <input type="number" name="elemento_precio[]" class="form-control form-control-sm f-pre" value="{{ $el->precio_unitario }}" placeholder="S/. Precio" min="0" step="1">
                        <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.closest('.cot-panel-row').remove()"><i class="bi bi-trash"></i></button>
                    </div>
                    @endforeach
                    <div class="cot-empty" id="empty-digital" {{ $cotizacion->elementos->where('tipo_elemento','digital')->count() ? 'style=display:none' : '' }}>
                        <i class="bi bi-display me-1 opacity-50"></i>Sin paneles digitales
                    </div>
                </div>
            </div>
            {{-- Tradicionales --}}
            <div class="cot-panel-group mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2" style="font-weight:600;font-size:13px">
                        <i class="bi bi-signpost-2" style="color:#EA580C;font-size:16px"></i> Tradicional Outdoor
                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning" id="cnt-tradicional">0</span>
                    </div>
                    <button type="button" class="cot-add-btn" onclick="addPanel('tradicional')"><i class="bi bi-plus-lg"></i> Agregar panel</button>
                </div>
                <div id="cont-tradicional">
                    @foreach($cotizacion->elementos->where('tipo_elemento','tradicional') as $el)
                    <div class="cot-panel-row">
                        <select name="elemento_panel_id[]" class="form-select form-select-sm">
                            <option value="">Seleccionar panel...</option>
                            @foreach($paneles_tradicionales as $pt)
                            <option value="{{ $pt->id }}" data-codigo="{{ $pt->codigo }}" {{ $el->panel_id == $pt->id ? 'selected' : '' }}>{{ $pt->codigo }} — {{ $pt->nombre }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="elemento_tipo[]" value="tradicional">
                        <input type="text" name="elemento_codigo[]" class="form-control form-control-sm f-cod" value="{{ $el->codigo }}" readonly>
                        <input type="number" name="elemento_tiempo[]" class="form-control form-control-sm f-mes" value="{{ $el->tiempo_contrato }}" placeholder="Meses" min="1">
                        <input type="number" name="elemento_precio[]" class="form-control form-control-sm f-pre" value="{{ $el->precio_unitario }}" placeholder="S/. Precio" min="0" step="1">
                        <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.closest('.cot-panel-row').remove()"><i class="bi bi-trash"></i></button>
                    </div>
                    @endforeach
                    <div class="cot-empty" id="empty-tradicional" {{ $cotizacion->elementos->where('tipo_elemento','tradicional')->count() ? 'style=display:none' : '' }}>
                        <i class="bi bi-signpost-2 me-1 opacity-50"></i>Sin paneles tradicionales
                    </div>
                </div>
            </div>
            {{-- Servicios --}}
            <div class="cot-panel-group">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2" style="font-weight:600;font-size:13px">
                        <i class="bi bi-box-seam" style="color:#7C3AED;font-size:16px"></i> Servicios Adicionales
                        <span class="badge rounded-pill" style="background:#EDE9FE;color:#7C3AED" id="cnt-servicio">0</span>
                    </div>
                    <button type="button" class="cot-add-btn" style="border-color:#7C3AED;color:#7C3AED" onclick="addServicio()"><i class="bi bi-plus-lg"></i> Agregar servicio</button>
                </div>
                <div id="cont-servicio">
                    @foreach($cotizacion->elementos->where('tipo_elemento','servicio') as $el)
                    <div class="cot-panel-row">
                        <select name="srv_id[]" class="form-select form-select-sm" style="flex:3">
                            <option value="">Seleccionar servicio...</option>
                            @foreach($servicios as $s)
                            <option value="{{ $s->id }}" data-monto="{{ $s->monto }}" {{ $el->servicio_id == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="srv_precio[]" class="form-control form-control-sm f-pre" value="{{ $el->precio_unitario }}" placeholder="S/. Precio" min="0" step="0.01">
                        <input type="text" name="srv_obs[]" class="form-control form-control-sm" value="{{ $el->observaciones }}" placeholder="Observaciones" style="flex:2">
                        <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.closest('.cot-panel-row').remove()"><i class="bi bi-trash"></i></button>
                    </div>
                    @endforeach
                    <div class="cot-empty" id="empty-servicio" {{ $cotizacion->elementos->where('tipo_elemento','servicio')->count() ? 'style=display:none' : '' }}>
                        <i class="bi bi-box-seam me-1 opacity-50"></i>Sin servicios adicionales
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="action-bar">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg"></i>Guardar cambios</button>
    </div>
</form>
</div>
@endsection

@push('styles')
<style>
.cot-section-title { display:flex;align-items:center;gap:8px;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:#374151;padding:10px 20px;background:#F8FAFC;border-bottom:1px solid #E2E8F0; }
.cot-section-title i { color:var(--primary);font-size:14px; }
.cot-add-btn { display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border:1px solid var(--primary);color:var(--primary);background:transparent;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer; }
.cot-add-btn:hover { background:#FEF2F2; }
.cot-empty { padding:12px;text-align:center;font-size:13px;color:#9CA3AF;border:1px dashed #D1D5DB;border-radius:8px; }
.cot-panel-row { display:flex;align-items:center;gap:8px;padding:8px 12px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;margin-bottom:6px; }
.cot-panel-row select { flex:2;min-width:0; }
.cot-panel-row .f-cod { width:90px;flex-shrink:0; }
.cot-panel-row .f-mes { width:80px;flex-shrink:0; }
.cot-panel-row .f-pre { width:110px;flex-shrink:0; }
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

function addPanel(tipo) {
    var cont  = document.getElementById('cont-' + tipo);
    var empty = document.getElementById('empty-' + tipo);
    if (empty) empty.style.display = 'none';
    var idx  = counters[tipo]++;
    var opts = '<option value="">Seleccionar panel...</option>' +
        paneles[tipo].map(function(p) {
            return '<option value="' + p.id + '" data-codigo="' + (p.codigo||'') + '">' + (p.codigo ? p.codigo + ' — ' : '') + p.nombre + '</option>';
        }).join('');
    var row = document.createElement('div');
    row.className = 'cot-panel-row';
    row.innerHTML =
        '<select name="elemento_panel_id[]" class="form-select form-select-sm" onchange="onSelect(this)">' + opts + '</select>' +
        '<input type="hidden" name="elemento_tipo[]" value="' + tipo + '">' +
        '<input type="text" name="elemento_codigo[]" class="form-control form-control-sm f-cod" placeholder="Código" readonly>' +
        '<input type="number" name="elemento_tiempo[]" class="form-control form-control-sm f-mes" placeholder="Meses" min="1">' +
        '<input type="number" name="elemento_precio[]" class="form-control form-control-sm f-pre" placeholder="S/. Precio" min="0" step="1">' +
        '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.closest(\'.cot-panel-row\').remove()"><i class="bi bi-trash"></i></button>';
    cont.appendChild(row);
}

function onSelect(sel) {
    var opt = sel.options[sel.selectedIndex];
    var row = sel.closest('.cot-panel-row');
    if (row) row.querySelector('input[name="elemento_codigo[]"]').value = opt.dataset.codigo || '';
}

function addServicio() {
    var cont  = document.getElementById('cont-servicio');
    var empty = document.getElementById('empty-servicio');
    if (empty) empty.style.display = 'none';
    var idx = counters['servicio']++;
    var opts = '<option value="">Seleccionar servicio...</option>' +
        serviciosDisp.map(function(s) {
            return '<option value="' + s.id + '" data-monto="' + s.monto + '">' + s.nombre + ' (S/. ' + parseFloat(s.monto).toFixed(2) + ')</option>';
        }).join('');
    var row = document.createElement('div');
    row.className = 'cot-panel-row';
    row.innerHTML =
        '<select name="srv_id[]" class="form-select form-select-sm" style="flex:3" onchange="onSelectSrv(this)">' + opts + '</select>' +
        '<input type="number" name="srv_precio[]" class="form-control form-control-sm f-pre" placeholder="S/. Precio" min="0" step="0.01">' +
        '<input type="text" name="srv_obs[]" class="form-control form-control-sm" placeholder="Observaciones" style="flex:2">' +
        '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.closest(\'.cot-panel-row\').remove()"><i class="bi bi-trash"></i></button>';
    cont.appendChild(row);
}

function onSelectSrv(sel) {
    var opt = sel.options[sel.selectedIndex];
    var row = sel.closest('.cot-panel-row');
    if (row && opt.dataset.monto) row.querySelector('input[name="srv_precio[]"]').value = parseFloat(opt.dataset.monto).toFixed(2);
}
</script>
@endpush
