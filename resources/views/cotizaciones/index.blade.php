@extends('layouts.app')

@section('title', 'Cotizaciones')

@section('content')

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-receipt"></i></div>
        <div>
            <div class="stat-value">{{ $stats_cot['total'] }}</div>
            <div class="stat-label">Total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-value">{{ $stats_cot['pendientes'] }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $stats_cot['aprobadas'] }}</div>
            <div class="stat-label">Aprobadas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-currency-dollar"></i></div>
        <div>
            <div class="stat-value" style="font-size:18px">S/. {{ number_format($stats_cot['monto'], 0, ',', '.') }}</div>
            <div class="stat-label">Monto activo</div>
        </div>
    </div>
</div>

{{-- Filtros + botón --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form class="d-flex flex-wrap gap-2 align-items-center" method="GET">
            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control"
                style="max-width:260px" placeholder="Buscar cliente, empresa, N°...">
            <select name="empresa_id" class="form-select" style="max-width:220px">
                <option value="">Todas las empresas</option>
                @foreach($empresas as $emp)
                    <option value="{{ $emp->id }}" {{ request('empresa_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->nombre }}
                    </option>
                @endforeach
            </select>
            <div class="d-flex gap-1 flex-wrap">
                @php
                $statusPills = [
                    '' => ['label'=>'Todos','cls'=>'sp-all'],
                    'pendiente'  => ['label'=>'Pendiente', 'cls'=>'sp-pen'],
                    'aprobada'   => ['label'=>'Aprobada',  'cls'=>'sp-apr'],
                    'rechazada'  => ['label'=>'Rechazada', 'cls'=>'sp-rec'],
                    'convertida' => ['label'=>'Convertida','cls'=>'sp-con'],
                ];
                @endphp
                @foreach($statusPills as $val => $pill)
                <a href="{{ route('cotizaciones.index', array_merge(request()->except('estado','page'), $val ? ['estado'=>$val] : [])) }}"
                   class="status-pill {{ $pill['cls'] }} {{ request('estado','') === $val ? 'active' : '' }}">
                    {{ $pill['label'] }}
                </a>
                @endforeach
            </div>
            @if(request()->hasAny(['buscar','empresa_id','estado']))
            <a href="{{ route('cotizaciones.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-x-circle me-1"></i>Limpiar
            </a>
            @endif
            <div class="ms-auto">
                @if(auth()->user()->esAdmin())
                <button type="button" class="btn btn-primary" onclick="abrirModal('modalNuevaCot')">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Cotización
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card" style="overflow:visible">
    <div class="card-header">
        <span><i class="bi bi-list-ul" style="color:var(--primary);margin-right:8px"></i>Lista de Cotizaciones</span>
        <span class="text-muted small">{{ $cotizaciones->total() }} cotización(es)</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cotizaciones as $cot)
                <tr>
                    <td>
                        <span class="badge bg-light border text-dark fw-600">{{ $cot->numero ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="fw-600">
                            @if($cot->empresa)
                                <a href="{{ route('empresas.show', $cot->empresa) }}" class="text-decoration-none">
                                    {{ $cot->empresa->nombre }}
                                </a>
                            @else
                                {{ $cot->cliente_nombre ?? $cot->cliente_empresa ?? '—' }}
                            @endif
                        </div>
                        @if($cot->cliente_nombre && $cot->empresa)
                            <div class="text-muted" style="font-size:12px">{{ $cot->cliente_nombre }}</div>
                        @endif
                        @if($cot->cliente_telefono)
                            <div class="text-muted" style="font-size:12px"><i class="bi bi-telephone me-1"></i>{{ $cot->cliente_telefono }}</div>
                        @endif
                    </td>
                    <td>
                        @if($cot->tipo_contrato)
                            <span class="badge badge-info" style="background:#EFF6FF;color:#2563EB;border:1px solid #BFDBFE">
                                {{ $cot->tipo_contrato }}
                            </span>
                        @else <span class="text-muted">—</span> @endif
                    </td>
                    <td class="fw-700" style="color:#059669">S/. {{ number_format($cot->monto_propuesto, 0, ',', '.') }}</td>
                    <td>
                        <div style="font-size:13px">{{ $cot->fecha_cotizacion?->format('d/m/Y') ?? '-' }}</div>
                        @if($cot->fecha_vencimiento)
                            <div class="text-muted" style="font-size:11px">
                                vence {{ $cot->fecha_vencimiento->format('d/m/Y') }}
                                @if($cot->estado === 'pendiente' && $cot->fecha_vencimiento->isPast())
                                    <span class="badge badge-danger ms-1" style="font-size:10px">Vencida</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        @php $estadoMap = ['pendiente'=>['warning','Pendiente'],'aprobada'=>['success','Aprobada'],'rechazada'=>['danger','Rechazada'],'convertida'=>['primary','Convertida']]; @endphp
                        @php [$bc, $bl] = $estadoMap[$cot->estado] ?? ['secondary', ucfirst($cot->estado)]; @endphp
                        <span class="badge badge-{{ $bc }}">{{ $bl }}</span>
                    </td>
                    <td class="text-end" style="white-space:nowrap">
                        <a href="{{ route('cotizaciones.show', $cot) }}" class="btn btn-sm btn-act btn-act-view" title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(auth()->user()->esAdmin())
                        <a href="{{ route('cotizaciones.edit', $cot) }}" class="btn btn-sm btn-act btn-act-edit ms-1" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if(in_array($cot->estado, ['pendiente', 'aprobada']))
                        <a href="{{ route('cotizaciones.convertir', $cot) }}" class="btn btn-sm btn-act btn-act-conv ms-1" title="Convertir a Contrato">
                            <i class="bi bi-arrow-right-circle"></i>
                        </a>
                        @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state" style="padding:40px">
                            <i class="bi bi-receipt"></i>
                            <p>No hay cotizaciones registradas</p>
                            @if(auth()->user()->esAdmin())
                            <button class="btn btn-primary btn-sm" onclick="abrirModal('modalNuevaCot')">
                                <i class="bi bi-plus-lg me-1"></i>Crear primera cotización
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cotizaciones->hasPages())
    <div class="card-footer">{{ $cotizaciones->withQueryString()->links() }}</div>
    @endif
</div>


{{-- ============================================================
     MODAL NUEVA COTIZACIÓN — usa el sistema custom del layout
     ============================================================ --}}
@if(auth()->user()->esAdmin())
<div class="modal-backdrop" id="modalNuevaCot" onclick="if(event.target===this)cerrarModal('modalNuevaCot')">
<div class="modal-box" style="max-width:900px;width:95%">

    <div class="modal-header">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-receipt" style="color:var(--primary);font-size:22px"></i>
            <div>
                <h5 class="mb-0">Nueva Cotización</h5>
                <div style="font-size:12px;opacity:.7;margin-top:2px">
                    Número asignado: <strong>{{ $numero }}</strong>
                </div>
            </div>
        </div>
        <button class="modal-close" onclick="cerrarModal('modalNuevaCot')">×</button>
    </div>

    <form action="{{ route('cotizaciones.store') }}" method="POST" id="formNuevaCot">
    @csrf
    <input type="hidden" name="numero" value="{{ $numero }}">

    <div class="modal-body" style="padding:0;max-height:70vh;overflow-y:auto">

        {{-- Sección 1 --}}
        <div class="cot-sec-title"><i class="bi bi-person-fill"></i> Datos del Cliente</div>
        <div style="padding:20px 28px;border-bottom:1px solid var(--border)">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium" style="font-size:13px">Empresa registrada en el sistema</label>
                    <select name="empresa_id" id="modal_empresa_id" class="form-select form-select-sm">
                        <option value="">— Cliente externo (no registrado) —</option>
                        @foreach($empresas as $emp)
                            <option value="{{ $emp->id }}"
                                data-nombre="{{ $emp->nombre }}"
                                data-encargado="{{ $emp->encargado ?? '' }}">
                                {{ $emp->nombre }}{{ $emp->encargado ? ' — '.$emp->encargado : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Nombre del contacto</label>
                    <input type="text" name="cliente_nombre" id="modal_cliente_nombre"
                        class="form-control form-control-sm" placeholder="Nombre completo">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Empresa / Negocio</label>
                    <input type="text" name="cliente_empresa" id="modal_cliente_empresa"
                        class="form-control form-control-sm" placeholder="Razón social">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Teléfono</label>
                    <input type="text" name="cliente_telefono" class="form-control form-control-sm" placeholder="999 999 999">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px">Email</label>
                    <input type="email" name="cliente_email" class="form-control form-control-sm" placeholder="correo@ejemplo.com">
                </div>
            </div>
        </div>

        {{-- Sección 2 --}}
        <div class="cot-sec-title"><i class="bi bi-file-invoice-dollar"></i> Datos de la Cotización</div>
        <div style="padding:20px 28px;border-bottom:1px solid var(--border)">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label" style="font-size:13px">Tipo de servicio</label>
                    <input type="text" name="tipo_contrato" class="form-control form-control-sm"
                        list="modal_tipos" placeholder="Panel Digital, Tradicional...">
                    <datalist id="modal_tipos">
                        <option value="Panel Digital">
                        <option value="Panel Tradicional">
                        <option value="Marketing Digital">
                        <option value="Mixto">
                    </datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:13px">Válida hasta</label>
                    <input type="date" name="fecha_vencimiento" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:13px">Notas / Descripción</label>
                    <textarea name="notas" class="form-control form-control-sm" rows="2"
                        placeholder="Detalles del servicio, condiciones..."></textarea>
                </div>
            </div>
        </div>

        {{-- Sección 3 --}}
        <div class="cot-sec-title"><i class="bi bi-geo-alt-fill"></i> Paneles de Interés</div>
        <div style="padding:20px 28px">

            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="font-weight:600;font-size:13px;display:flex;align-items:center;gap:6px">
                        <i class="bi bi-display" style="color:#2563EB"></i> Digital Outdoor
                        <span id="m-cnt-digital" style="background:#EFF6FF;color:#2563EB;padding:1px 8px;border-radius:20px;font-size:11px">0</span>
                    </div>
                    <button type="button" class="cot-add-btn" onclick="mAddPanel('digital')">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
                <div id="m-cont-digital">
                    <div class="cot-empty" id="m-empty-digital">
                        <i class="bi bi-display me-1" style="opacity:.4"></i>Sin paneles digitales
                    </div>
                </div>
            </div>

            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div style="font-weight:600;font-size:13px;display:flex;align-items:center;gap:6px">
                        <i class="bi bi-signpost-2" style="color:#EA580C"></i> Tradicional Outdoor
                        <span id="m-cnt-tradicional" style="background:#FFF7ED;color:#EA580C;padding:1px 8px;border-radius:20px;font-size:11px">0</span>
                    </div>
                    <button type="button" class="cot-add-btn" onclick="mAddPanel('tradicional')">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
                <div id="m-cont-tradicional">
                    <div class="cot-empty" id="m-empty-tradicional">
                        <i class="bi bi-signpost-2 me-1" style="opacity:.4"></i>Sin paneles tradicionales
                    </div>
                </div>
            </div>

        </div>
    </div>{{-- /modal-body --}}

    {{-- Resumen IGV --}}
    <div style="padding:10px 28px;background:#F8FAFC;border-top:1px solid #E2E8F0">
        <div style="display:flex;justify-content:flex-end">
            <table style="font-size:12px;min-width:240px">
                <tr>
                    <td style="padding:3px 12px 3px 0;color:#64748B">Subtotal neto</td>
                    <td style="font-weight:600;text-align:right">S/. <span id="m-sub">0.00</span></td>
                </tr>
                <tr>
                    <td style="padding:3px 12px 3px 0;color:#64748B">IGV (18%)</td>
                    <td style="font-weight:600;text-align:right">S/. <span id="m-igv">0.00</span></td>
                </tr>
                <tr style="border-top:2px solid #E2E8F0">
                    <td style="padding:6px 12px 0 0;font-weight:700;font-size:13px">TOTAL CON IGV</td>
                    <td style="font-weight:800;text-align:right;font-size:13px;color:#059669;padding-top:6px">S/. <span id="m-total">0.00</span></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalNuevaCot')">
            <i class="bi bi-x-lg me-1"></i>Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>Guardar Cotización
        </button>
    </div>

    </form>
</div>
</div>
@endif

@endsection

@push('styles')
<style>
/* ── Status filter pills ─────────────────────────── */
.status-pill {
    display: inline-flex; align-items: center; border-radius: 20px;
    font-size: 11.5px; padding: 5px 14px; font-weight: 600;
    border: 1.5px solid transparent; cursor: pointer; text-decoration: none;
    transition: all .18s ease;
}
.status-pill:hover { transform: translateY(-1px); text-decoration: none; }
.status-pill.sp-all          { background:#F1F5F9; color:#64748B; border-color:#E2E8F0; }
.status-pill.sp-all.active   { background:#334155; color:#fff; border-color:#334155; box-shadow:0 4px 10px rgba(51,65,85,.25); }
.status-pill.sp-pen          { background:#FFFBEB; color:#92400E; border-color:#FDE68A; }
.status-pill.sp-pen.active   { background:#D97706; color:#fff; border-color:#B45309; box-shadow:0 4px 10px rgba(217,119,6,.3); }
.status-pill.sp-apr          { background:#F0FDF4; color:#166534; border-color:#BBF7D0; }
.status-pill.sp-apr.active   { background:#16A34A; color:#fff; border-color:#15803D; box-shadow:0 4px 10px rgba(22,163,74,.3); }
.status-pill.sp-rec          { background:#FFF1F2; color:#9F1239; border-color:#FECDD3; }
.status-pill.sp-rec.active   { background:#E11D48; color:#fff; border-color:#BE123C; box-shadow:0 4px 10px rgba(225,29,72,.3); }
.status-pill.sp-con          { background:#EFF6FF; color:#1E40AF; border-color:#BFDBFE; }
.status-pill.sp-con.active   { background:#2563EB; color:#fff; border-color:#1D4ED8; box-shadow:0 4px 10px rgba(37,99,235,.3); }
/* ── Colored action buttons ──────────────────────── */
.btn-act { border-radius: var(--radius-sm); font-size: 12px; padding: 5px 10px; border-width: 1.5px; border-style: solid; transition: all .18s ease; display: inline-flex; align-items: center; justify-content: center; }
.btn-act:hover { transform: translateY(-1px); text-decoration: none; }
.btn-act-view { color:#2563EB; border-color:#BFDBFE; background:rgba(219,234,254,.45); }
.btn-act-view:hover { background:#DBEAFE; border-color:#93C5FD; color:#1D4ED8; box-shadow:0 4px 12px rgba(37,99,235,.18); }
.btn-act-edit { color:#D97706; border-color:#FDE68A; background:rgba(254,243,199,.45); }
.btn-act-edit:hover { background:#FEF3C7; border-color:#FCD34D; color:#B45309; box-shadow:0 4px 12px rgba(217,119,6,.18); }
.btn-act-conv { color:#059669; border-color:#A7F3D0; background:rgba(209,250,229,.45); }
.btn-act-conv:hover { background:#D1FAE5; border-color:#6EE7B7; color:#047857; box-shadow:0 4px 12px rgba(5,150,105,.18); }
/* ── Modal section titles ────────────────────────── */
.cot-sec-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--primary-dark);
    padding: 12px 28px;
    background: linear-gradient(90deg, rgba(230,57,70,0.06), rgba(255,255,255,0.7));
    border-left: 4px solid var(--primary);
    border-bottom: 1px solid rgba(226,232,240,.6);
}
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
.cot-panel-row select { flex: 3; min-width: 200px; }
.cot-panel-row .f-cod  { width: 76px; flex-shrink: 0; }
.cot-panel-row .f-mes  { width: 70px; flex-shrink: 0; }
.cot-panel-row .f-pre  { width: 96px; flex-shrink: 0; }
.cot-panel-row .f-desc { flex: 2; min-width: 150px; }
</style>
@endpush

@push('scripts')
<script>
function abrirModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

@php
$_mPanelesDigital = $paneles_digitales->map(function($p) {
    return ['id' => $p->id, 'codigo' => $p->codigo, 'nombre' => $p->nombre, 'costo' => $p->costo_produccion ?? 0, 'desc' => $p->desc_costo ?? 'Instalación y puesta en marcha'];
})->values();
$_mPanelesTradicional = $paneles_tradicionales->map(function($p) {
    return ['id' => $p->id, 'codigo' => $p->codigo, 'nombre' => $p->nombre, 'costo' => $p->costo_produccion ?? 0, 'desc' => $p->desc_costo ?? 'Producción de lona e instalación'];
})->values();
@endphp
var mPaneles = {
    digital:     @json($_mPanelesDigital),
    tradicional: @json($_mPanelesTradicional)
};
var mCounters = { digital: 0, tradicional: 0 };
var M_IGV = 0.18;

function mRecalc() {
    var sub = 0;
    document.querySelectorAll('#modalNuevaCot input[name="elemento_precio[]"]').forEach(function(i){ sub += parseFloat(i.value)||0; });
    document.querySelectorAll('#modalNuevaCot input[name="elemento_costo[]"]').forEach(function(i){ sub += parseFloat(i.value)||0; });
    var igv = sub * M_IGV;
    document.getElementById('m-sub').textContent    = sub.toFixed(2);
    document.getElementById('m-igv').textContent    = igv.toFixed(2);
    document.getElementById('m-total').textContent  = (sub + igv).toFixed(2);
}

function mUpdateCount(tipo) {
    document.getElementById('m-cnt-' + tipo).textContent =
        document.getElementById('m-cont-' + tipo).querySelectorAll('.cot-panel-row').length;
}

function mAddPanel(tipo) {
    var cont  = document.getElementById('m-cont-' + tipo);
    var empty = document.getElementById('m-empty-' + tipo);
    if (empty) empty.style.display = 'none';
    var idx  = mCounters[tipo]++;
    var opts = '<option value="">Seleccionar panel...</option>' +
        mPaneles[tipo].map(function(p) {
            return '<option value="'+p.id+'" data-codigo="'+(p.codigo||'')+'" data-costo="'+(p.costo||0)+'" data-desc="'+(p.desc||'')+'">'+(p.codigo ? p.codigo+' — ' : '')+p.nombre+'</option>';
        }).join('');
    var descDefault = tipo === 'tradicional' ? 'Producción de lona e instalación' : 'Instalación y puesta en marcha';
    var row = document.createElement('div');
    row.className = 'cot-panel-row flex-wrap';
    row.id = 'm-row-'+tipo+'-'+idx;
    row.innerHTML =
        '<select name="elemento_panel_id[]" class="form-select form-select-sm" style="flex:3;min-width:200px" onchange="mOnSelect(this,\''+tipo+'\','+idx+')">'+opts+'</select>'+
        '<input type="hidden" name="elemento_tipo[]" value="'+tipo+'">'+
        '<input type="text"   name="elemento_codigo[]" class="form-control form-control-sm f-cod" placeholder="Código" readonly>'+
        '<input type="number" name="elemento_tiempo[]" class="form-control form-control-sm f-mes" placeholder="Meses" min="1" oninput="mRecalc()">'+
        '<input type="number" name="elemento_precio[]" class="form-control form-control-sm f-pre" placeholder="S/. Precio" min="0" step="0.01" oninput="mRecalc()">'+
        '<input type="number" name="elemento_costo[]"  class="form-control form-control-sm f-pre" placeholder="S/. Costo" min="0" step="0.01" oninput="mRecalc()">'+
        '<input type="text"   name="elemento_desc_costo[]" class="form-control form-control-sm f-desc" placeholder="Desc. costo" value="'+descDefault+'">'+
        '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="mRemovePanel(\''+tipo+'\','+idx+')"><i class="bi bi-trash"></i></button>';
    cont.appendChild(row);
    mUpdateCount(tipo);
}

function mOnSelect(sel, tipo, idx) {
    var opt = sel.options[sel.selectedIndex];
    var row = document.getElementById('m-row-'+tipo+'-'+idx);
    if (!row) return;
    row.querySelector('[name="elemento_codigo[]"]').value = opt.dataset.codigo || '';
    var costoInp = row.querySelector('[name="elemento_costo[]"]');
    if (costoInp && opt.dataset.costo !== undefined) costoInp.value = parseFloat(opt.dataset.costo||0).toFixed(2);
    var descInp = row.querySelector('[name="elemento_desc_costo[]"]');
    if (descInp && opt.dataset.desc) descInp.value = opt.dataset.desc;
    mRecalc();
}

function mRemovePanel(tipo, idx) {
    var row = document.getElementById('m-row-'+tipo+'-'+idx);
    if (row) row.remove();
    var cont = document.getElementById('m-cont-'+tipo);
    if (!cont.querySelector('.cot-panel-row'))
        document.getElementById('m-empty-'+tipo).style.display = '';
    mUpdateCount(tipo);
    mRecalc();
}

document.getElementById('modal_empresa_id').addEventListener('change', function () {
    var opt = this.options[this.selectedIndex];
    document.getElementById('modal_cliente_nombre').value  = opt.dataset.encargado || '';
    document.getElementById('modal_cliente_empresa').value = opt.dataset.nombre    || '';
});
</script>
@endpush
